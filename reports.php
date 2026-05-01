<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database Connection
$conn = new mysqli("localhost", "choolweg_kabwe_council_db", "gambwe1997", "choolweg_kabwe_council_db");

// General Stats
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM service_requests";
$stats = $conn->query($stats_query)->fetch_assoc();

// Get Service Type
$service_dist = $conn->query("SELECT service_type, COUNT(*) as count 
                              FROM service_requests 
                              GROUP BY service_type 
                              ORDER BY count DESC");

//  Get Recent Activity Trend (Last 7 Days)
$trend = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count 
                       FROM service_requests 
                       WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                       GROUP BY DATE(created_at)");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --blue: #2563eb; --bg: #f8fafc; --text: #1e293b; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 260px; background: #0f172a; color: white; height: 100vh; padding: 20px; position: sticky; top: 0; }
        .main { flex: 1; padding: 40px; }

        /* Grid Layout for Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid var(--blue); }
        .stat-card h3 { margin: 0; color: #64748b; font-size: 14px; text-transform: uppercase; }
        .stat-card p { margin: 10px 0 0; font-size: 28px; font-weight: bold; color: var(--text); }

        .report-section { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #edf2f7; color: #64748b; }
        td { padding: 12px; border-bottom: 1px solid #edf2f7; }

        .btn-print { background: var(--blue); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; float: right; }
        @media print { .sidebar, .btn-print { display: none; } .main { padding: 0; } }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2>KMC ADMIN</h2><br>
    <a href="admin_dashboard.php" style="color:#94a3b8; text-decoration:none;"> Dashboard</a><br><br>
    <a href="admin_reports.php" style="color:white; text-decoration:none; font-weight:bold;"> Reports</a>
</nav>

<main class="main">
    <a href="#" class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print Report</a>
    <h1>System Reports</h1>
    <p style="color: #64748b;">Generated on: <?php echo date('F j, Y, g:i a'); ?></p>

    <div class="stats-grid">
        <div class="stat-card"><h3>Total Requests</h3><p><?php echo $stats['total']; ?></p></div>
        <div class="stat-card" style="border-color: #10b981;"><h3>Approved</h3><p><?php echo $stats['approved']; ?></p></div>
        <div class="stat-card" style="border-color: #f59e0b;"><h3>Pending</h3><p><?php echo $stats['pending']; ?></p></div>
        <div class="stat-card" style="border-color: #ef4444;"><h3>Rejected</h3><p><?php echo $stats['rejected']; ?></p></div>
    </div>

    <div class="report-section">
        <div class="card">
            <h3>Requests by Service Type</h3>
            <table>
                <thead><tr><th>Service</th><th>Count</th><th>Percentage</th></tr></thead>
                <tbody>
                    <?php while($row = $service_dist->fetch_assoc()): 
                        $percentage = ($stats['total'] > 0) ? round(($row['count'] / $stats['total']) * 100, 1) : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                        <td><?php echo $row['count']; ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Volume Trend (Last 7 Days)</h3>
            <table>
                <thead><tr><th>Date</th><th>Applications</th></tr></thead>
                <tbody>
                    <?php while($row = $trend->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                        <td><strong><?php echo $row['count']; ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>