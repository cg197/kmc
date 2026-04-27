<?php
// admin_verify.php
include 'db_connect.php'; // Make sure you have your connection details

if (isset($_GET['id']) && isset($_GET['status'])) {
    $payment_id = $_GET['id'];
    $new_status = $_GET['status']; // 'verified' or 'failed'

    $stmt = $conn->prepare("UPDATE payments SET status = ? WHERE payment_id = ?");
    $stmt->bind_param("si", $new_status, $payment_id);

    if ($stmt->execute()) {
        echo "Payment updated successfully!";
        // Redirect back to admin panel
        header("Location: admin_dashboard.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
	// If payment is successful, also update the service request status
if ($new_status == 'verified') {
    // First, find the request_id associated with this payment
    $result = $conn->query("SELECT request_id FROM payments WHERE payment_id = $payment_id");
    $row = $result->fetch_assoc();
    $req_id = $row['request_id'];

    // Update the service_requests table
    $conn->query("UPDATE service_requests SET status = 'completed' WHERE request_id = $req_id");
}
?>