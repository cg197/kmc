<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: log_in.php"); exit(); }

// Database connection
$host = "localhost"; 
$db_user = "choolweg_kabwe_council_db";
$db_pass = "gambwe1997";
$db_name = "choolweg_kabwe_council_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Get the Payment ID from the URL
$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($payment_id > 0) {
    $sql = "SELECT p.*, u.full_name 
            FROM payments p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE p.payment_id = $payment_id";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        die("Error: Payment record not found.");
    }
} else {
    die("Error: Invalid Payment ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - #<?php echo $data['payment_id']; ?></title>
    <style>
        body { font-family: sans-serif; padding: 50px; background: #f9f9f9; }
        .receipt-box { max-width: 600px; margin: auto; background: white; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 20px; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px outline #f1f1f1; }
        .label { color: #666; font-weight: bold; }
        .btn-print { background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; width: 100%; }
    </style>
</head>
<body>

<div class="receipt-box">
    <div class="header">
        <h2>Kabwe Municipal Council</h2>
        <p>Official Payment Receipt</p>
    </div>

    <div class="row"><span class="label">Payment ID:</span> <span>#PAY-<?php echo $data['payment_id']; ?></span></div>
    <div class="row"><span class="label">Customer:</span> <span><?php echo $data['full_name']; ?></span></div>
    <div class="row"><span class="label">Method:</span> <span><?php echo $data['method']; ?></span></div>
    <div class="row"><span class="label">Reference:</span> <span><?php echo $data['reference']; ?></span></div>
    <div class="row"><span class="label">Amount Paid:</span> <span style="font-weight:bold;">K <?php echo number_format($data['amount'], 2); ?></span></div>
    <div class="row"><span class="label">Status:</span> <span style="color:green;"><?php echo strtoupper($data['status']); ?></span></div>
    <div class="row"><span class="label">Date:</span> <span><?php echo $data['payment_date']; ?></span></div>

    <button class="btn-print" onclick="window.print()">Print Receipt</button>
</div>
<a href="payments.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> back</a>
</body>
</html>