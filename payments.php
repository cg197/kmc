<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// --- connection ---
$host = "localhost"; 
$db_user = "choolweg_kabwe_council_db";
$db_pass = "gambwe1997";
$db_name = "choolweg_kabwe_council_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

$user_id = $_SESSION['user_id'];
$message = "";

// --- SINGLE HANDLE PAYMENT SUBMISSION BLOCK ---
if (isset($_GET['pay_id']) && isset($_GET['amt']) && isset($_GET['ref'])) {
    $amount = $_GET['amt'];
    $request_id = $_GET['pay_id'];
    $method = $_GET['method'];
    $ref = $_GET['ref'];

    if (empty($ref)) {
        $message = "Error: Reference number is missing.";
    } else {
        // We include all required columns based on your table structure
        $stmt = $conn->prepare("INSERT INTO payments (user_id, amount, method, status, reference, payment_type, request_id) VALUES (?, ?, ?, 'pending', ?, ?, ?)");
        
        $payment_type = "Service Fee"; 
        
        // i=user_id, d=amount, s=method, s=ref, s=ref_no, s=type, s=req_id
        $stmt->bind_param("idssss", $user_id, $amount, $method, $ref, $payment_type, $request_id);

        if ($stmt->execute()) {
            $message = "Success! Payment of K $amount (Ref: $ref) is pending verification.";
        } else {
            $message = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- FETCH DATA ---
$pending_query = "SELECT * FROM service_requests WHERE user_id = $user_id AND status = 'approved'";
$pending_result = $conn->query($pending_query);
$total_balance = $pending_result->num_rows * 150;

$history_query = "SELECT * FROM payments WHERE user_id = $user_id ORDER BY payment_id DESC";
$history_result = $conn->query($history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC - Payments & Billing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb; --sidebar-bg: #0f172a; --bg-light: #f1f5f9;
            --white: #ffffff; --text-main: #1e293b; --text-muted: #64748b;
            --danger: #ef4444; --success: #10b981; --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-main); }
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 2rem 1.5rem; position: sticky; top: 0; height: 100vh; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 0.5rem; }
        .nav-item.active { background: var(--primary); color: white; }
        .content { flex: 1; padding: 2rem 3rem; }
        .balance-card { background: linear-gradient(135deg, var(--sidebar-bg) 0%, #1e293b 100%); color: white; padding: 2rem; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .table-section { background: var(--white); border-radius: 16px; padding: 1.5rem; border: 1px solid var(--border); margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: var(--text-muted); font-size: 0.75rem; padding: 1rem; border-bottom: 1px solid var(--border); }
        td { padding: 1.25rem 1rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .unpaid { background: #fee2e2; color: var(--danger); }
        .paid { background: #dcfce7; color: var(--success); }
        .pending { background: #fef3c7; color: #92400e; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal { background: white; padding: 2rem; border-radius: 20px; width: 400px; text-align: center; }
        input { width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: 8px; margin-top: 5px; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:3rem;">
            <i class="fas fa-landmark" style="color:var(--primary)"></i> <h2>KMC PORTAL</h2>
        </div>
        <nav>
            <a href="user_dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="payments.php" class="nav-item active"><i class="fas fa-wallet"></i> Payments</a>
            <a href="user_dashboard.php" class="nav-item"><i class="fas fa-arrow-left"></i> Back</a>
        </nav>
    </aside>

    <main class="content">
        <?php if($message): ?>
            <div style="background:var(--primary); color:white; padding:15px; border-radius:10px; margin-bottom:20px;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="balance-card">
            <div>
                <p style="opacity: 0.8;">Outstanding Balance</p>
                <p style="font-size:2.5rem; font-weight:700;">K <?php echo number_format($total_balance, 2); ?></p>
            </div>
            <button onclick="openPaymentModal('<?php echo $total_balance; ?>', 'ALL')" style="background:var(--primary); color:white; border:none; padding:12px 24px; border-radius:10px; cursor:pointer;">Pay Total Balance</button>
        </div>

        <section class="table-section">
            <h3>Pending Invoices</h3>
            <table>
                <thead>
                    <tr><th>Service</th><th>Due Date</th><th>Amount</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if($pending_result && $pending_result->num_rows > 0): ?>
                        <?php while($row = $pending_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['service_type']; ?></td>
                            <td><?php echo $row['date_submitted']; ?></td>
                            <td>K 150.00</td>
                            <td><span class="status-pill unpaid">Unpaid</span></td>
                            <td><button onclick="openPaymentModal('150', '<?php echo $row['request_id']; ?>')" style="background:var(--primary); color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer;">Pay Now</button></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No outstanding invoices.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="table-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3>Recent Transactions</h3>
            </div>
            <table>
                <thead>
                    <tr><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if($history_result && $history_result->num_rows > 0): ?>
                        <?php while($p = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo isset($p['payment_date']) ? date('d/m/y', strtotime($p['payment_date'])) : 'Recent'; ?></td>
                            <td><?php echo htmlspecialchars($p['method'] ?? 'N/A'); ?></td> 
                            <td><?php echo htmlspecialchars($p['reference'] ?? 'N/A'); ?></td>
                            <td>K <?php echo number_format($p['amount'], 2); ?></td>
                            <td><span class="status-pill <?php echo strtolower($p['status']); ?>"><?php echo ucfirst($p['status']); ?></span></td>
                            <td><a href="receipt.php?id=<?php echo $p['payment_id']; ?>" target="_blank" style="color:var(--primary);"><i class="fas fa-print"></i></a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center;">No transaction history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <div class="modal-overlay" id="paymentModal">
        <div class="modal">
            <h3>Complete Payment</h3>
            <div style="text-align: left; margin: 15px 0;">
                <label style="font-size: 0.8rem; font-weight: bold;">Amount (K)</label>
                <input type="number" id="inputAmount" readonly>
                
                <label style="font-size: 0.8rem; font-weight: bold; display:block; margin-top:10px;">Phone Number / Reference</label>
                <input type="text" placeholder="e.g. 097XXXXXXX" id="reference_no">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:20px;">
                <button onclick="processRealPayment('Mobile Money')" style="padding:10px; border:1px solid var(--primary); background:#eff6ff; border-radius:8px; cursor:pointer;">Mobile Money</button>
                <button onclick="processRealPayment('Bank Transfer')" style="padding:10px; border:1px solid var(--primary); background:#eff6ff; border-radius:8px; cursor:pointer;">Bank Transfer</button>
            </div>
            <button onclick="closeModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer;">Cancel</button>
        </div>
    </div>

    <script>
        let currentId = 0;

        function openPaymentModal(amount, id) {
            currentId = id;
            document.getElementById('inputAmount').value = amount;
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closeModal() { 
            document.getElementById('paymentModal').style.display = 'none'; 
        }

        function processRealPayment(method) {
            const amt = document.getElementById('inputAmount').value;
            const ref = document.getElementById('reference_no').value; 
            
            if(!amt || !ref) { 
                alert("Please enter the Phone Number/Reference"); 
                return; 
            }
            
            // Redirect with the data in the URL
            window.location.href = `payments.php?pay_id=${currentId}&amt=${amt}&method=${method}&ref=${encodeURIComponent(ref)}`;
        }
    </script>
</body>
</html>