<?php
require_once 'functions.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// 2. Database Connection
$host = "localhost"; $db_user = "choolweg_kabwe_council_db"; $db_pass = "gambwe1997"; $db_name = "choolweg_kabwe_council_db";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// 3. Fetch Data Metrics
$revenue_res = $conn->query("SELECT SUM(amount) as total FROM payments WHERE status = 'success'");
$total_revenue = $revenue_res->fetch_assoc()['total'] ?? 0;

$total_apps = $conn->query("SELECT COUNT(*) as total FROM service_requests")->fetch_assoc()['total'];
$pending_apps = $conn->query("SELECT COUNT(*) as total FROM service_requests WHERE status = 'pending'")->fetch_assoc()['total'];

$recent_tx = $conn->query("
    SELECT p.*, u.full_name 
    FROM payments p 
    JOIN users u ON p.user_id = u.user_id 
    WHERE p.status = 'success' 
    ORDER BY p.payment_id DESC LIMIT 15
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC Official Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #0f172a; --blue: #2563eb; --bg: #f8fafc; --success: #10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; margin: 0; }
        
        /* Screen UI */
        .sidebar { width: 260px; background: var(--sidebar); color: white; padding: 20px; height: 100vh; position: sticky; top: 0; }
        .nav-link { display: block; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .nav-link.active { background: var(--blue); color: white; }
        .main { flex: 1; padding: 40px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid var(--blue); }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #64748b; font-size: 12px; }
        td { padding: 12px; border-bottom: 1px solid #edf2f7; font-size: 14px; }
        
        .print-header { display: none; text-align: center; margin-bottom: 30px; }
        .btn-print { background: var(--sidebar); color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; }

        /* --- PRINT LOGIC --- */
        @media print {
            .sidebar, .btn-print, .nav-link, .no-print { display: none !important; }
            body { background: white; }
            .main { padding: 0; width: 100%; }
            .print-header { display: block !important; }
            .card, .stat-card { box-shadow: none !important; border: 1px solid #eee !important; }
            .stat-card { border-left: 5px solid #000 !important; }
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2>KMC ADMIN</h2><br>
    <a href="admin_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin_reports.php" class="nav-link active"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="logout.php" class="nav-link" style="color:#ef4444; margin-top: 40px;"><i class="fas fa-power-off"></i> Logout</a>
</nav>

<main class="main">
    <div class="print-header">
        <img src="logo.png" alt="KMC Logo" style="width: 80px;"> <h2>KABWE MUNICIPAL COUNCIL</h2>
        <p>Official Revenue & Activity Report</p>
        <p><strong>Date Generated:</strong> <?php echo date('d F Y, H:i'); ?></p>
        <hr>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;" class="no-print">
        <h1>Council Reports</h1>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Official Report
        </button>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <small>TOTAL REVENUE</small>
            <h2 style="color: var(--success);">K<?php echo number_format($total_revenue, 2); ?></h2>
        </div>
        <div class="stat-card">
            <small>TOTAL APPLICATIONS</small>
            <h2><?php echo $total_apps; ?></h2>
        </div>
        <div class="stat-card">
            <small>PENDING REVIEW</small>
            <h2 style="color: #f59e0b;"><?php echo $pending_apps; ?></h2>
        </div>
    </div>

    <div class="card">
        <h3><i class="fas fa-list"></i> Recent Successful Payments</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Citizen Name</th>
                    <th>Reference</th>
                    <th>Method</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while($tx = $recent_tx->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($tx['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($tx['full_name']); ?></td>
                    <td><code><?php echo htmlspecialchars($tx['reference']); ?></code></td>
                    <td><?php echo $tx['method']; ?></td>
                    <td><strong>K<?php echo number_format($tx['amount'], 2); ?></strong></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <p style="margin-top: 30px; font-size: 12px; color: #94a3b8; text-align: center;" class="print-header">
        This is a system-generated report from the KMC Administrative Portal.
    </p>
</main>

</body>
</html>