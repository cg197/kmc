<?php
session_start();
require_once 'config.php'; // Using your PDO connection

// Security: Ensure only admins can access this (Adjust to your session logic)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if (isset($_POST['approve_payment'])) {
    $payment_id = $_POST['payment_id'];
    $request_id = $_POST['request_id'];

    try {
        $pdo->beginTransaction();

        // 1. Update Payment status to 'success'
        $stmt1 = $pdo->prepare("UPDATE payments SET status = 'success' WHERE payment_id = ?");
        $stmt1->execute([$payment_id]);

        // 2. Update Service Request status to 'paid' (or 'final_approval')
        $stmt2 = $pdo->prepare("UPDATE service_requests SET status = 'paid' WHERE request_id = ?");
        $stmt2->execute([$request_id]);

        // 3. Update Document status to 'Approved' so user can download
        $stmt3 = $pdo->prepare("UPDATE documents SET status = 'Approved' WHERE request_id = ?");
        $stmt3->execute([$request_id]);

        $pdo->commit();
        header("Location: admin_dashboard.php?msg=ApprovedSuccessfully");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Approval failed: " . $e->getMessage());
    }
}
?>