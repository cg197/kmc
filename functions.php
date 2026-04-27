<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Admin log-in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database Connection
$host = "localhost"; $db_user = "choolweg_kabwe_council_db"; $db_pass = "gambwe1997"; $db_name = "choolweg_kabwe_council_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Your SMS function
function sendSMS($phone, $message) {
    // ... (the SMS code from earlier)
}

// A helper for formatting currency
function formatKwatcha($amount) {
    return "ZMW " . number_format($amount, 2);
}

function triggerStatusAlert($conn, $request_id, $new_status) {
    //  Fetch User and Request Details
    $stmt = $conn->prepare("
        SELECT u.user_id, u.email, u.phone, u.full_name, sr.service_type 
        FROM users u 
        JOIN service_requests sr ON u.user_id = sr.user_id 
        WHERE sr.request_id = ?
    ");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        $subject = "KMC Update: " . $user['service_type'];
        $message = "Hello " . $user['full_name'] . ", your application for " . $user['service_type'] . " has been updated to: " . strtoupper($new_status) . ".";

        // Database Notification
        $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
        $notif_stmt->bind_param("iss", $user['user_id'], $subject, $message);
        $notif_stmt->execute();

        //  Send Email
        sendEmailAlert($user['email'], $subject, $message);

        //  Send SMS (Critical updates)
        if (in_array($new_status, ['approved', 'rejected'])) {
            $sms_msg = "KMC Alert: Your " . $user['service_type'] . " status is now " . strtoupper($new_status) . ". Check portal for details.";
            sendSMS($user['phone'], $sms_msg);
        }
    }
}

// Email Alert Function
function sendEmailAlert($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@kmc.gov.zm';
        $mail->Password   = 'your_password';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('noreply@kmc.gov.zm', 'Kabwe Municipal Council');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "<div style='font-family: Arial, sans-serif; border: 1px solid #e2e8f0; padding: 20px; border-radius: 10px;'>
                            <h2 style='color: #2563eb;'>Kabwe Municipal Council</h2>
                            <p>$body</p>
                            <hr style='border: 0; border-top: 1px solid #eee;'>
                            <small style='color: #64748b;'>This is an automated message. Please do not reply.</small>
                          </div>";
        $mail->send();
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Email Error: " . $e->getMessage());
    }
}

// SMS Alert Function
function sendSMSAlert($phone, $message) {
    error_log("SMS sent to $phone: $message");
}

//  Approval/Rejection Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    // Update the database
    $stmt = $conn->prepare("UPDATE service_requests SET status = ? WHERE request_id = ?");
    $stmt->bind_param("si", $status, $id);
    if ($stmt->execute()) {
        // TRIGGER ALERTS
        triggerStatusAlert($conn, $id, $status);
        header("Location: admin_dashboard.php?msg=updated");
        exit();
    }
}

// requests for the table
$result = $conn->query("
    SELECT sr.request_id, sr.service_type, u.full_name, sr.status, sr.created_at
    FROM service_requests sr
    JOIN users u ON sr.user_id = u.user_id
    ORDER BY sr.created_at DESC
");
?>