<?php
session_start();

// 1. SESSION CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];


if ($user_role === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC | Citizen Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --sidebar-bg: #0f172a;
            --bg-light: #f1f5f9;
            --white: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); display: flex; min-height: 100vh; color: var(--text-main); }

        /* --- Sidebar --- */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: white; padding: 2rem 1.5rem; position: sticky; top: 0; height: 100vh; }
        .logo-area { display: flex; align-items: center; gap: 12px; margin-bottom: 3rem; }
        .logo-icon { width: 32px; height: 32px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 0.5rem; transition: 0.2s; }
        .nav-item:hover, .nav-item.active { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-item.active { background: var(--primary); }

        /* --- Main Layout --- */
        .content { flex: 1; padding: 2rem 3rem; overflow-y: auto; }
        .welcome-header { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        .welcome-header h1 { font-size: 1.8rem; font-weight: 800; text-transform: uppercase; }

        .alert-banner {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            color: #9a3412;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        /* --- Grid Layout --- */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }

        /* --- Quick Actions --- */
        .action-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .action-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: 0.3s;
        }
        .action-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .action-card i { font-size: 1.5rem; color: var(--primary); margin-bottom: 10px; }
        .action-card span { display: block; font-weight: 700; font-size: 0.9rem; }

        /* --- Summary Section --- */
        .summary-box { background: var(--white); border-radius: 20px; padding: 1.5rem; border: 1px solid var(--border); margin-bottom: 2rem; }
        .summary-box h3 { margin-bottom: 1.5rem; font-size: 1.1rem; }

        /* --- Timeline --- */
        .timeline { position: relative; padding-left: 30px; }
        .timeline::before { content: ''; position: absolute; left: 7px; top: 0; height: 100%; width: 2px; background: var(--border); }
        .timeline-item { position: relative; margin-bottom: 1.5rem; }
        .timeline-item::after { content: ''; position: absolute; left: -27px; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); border: 3px solid white; }
        .timeline-date { font-size: 0.75rem; color: var(--text-muted); display: block; }
        .timeline-text { font-size: 0.9rem; font-weight: 600; }

        /* --- Right Column Widgets --- */
        .widget { background: var(--white); border-radius: 20px; padding: 1.5rem; border: 1px solid var(--border); margin-bottom: 1.5rem; }
        .widget-title { font-weight: 700; margin-bottom: 1rem; display: flex; justify-content: space-between; font-size: 0.9rem; }
        .amount-due { font-size: 1.8rem; font-weight: 800; color: var(--danger); }
        .btn-small { padding: 10px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; border: none; cursor: pointer; transition: 0.2s; }
        .btn-small:hover { opacity: 0.9; }

        /* --- Table Styles --- */
        .table-container { background: var(--white); border-radius: 16px; border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; padding: 1rem; background: #f8fafc; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }
        tbody td { padding: 1rem; border-top: 1px solid var(--border); font-size: 0.9rem; }

        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo-area">
            <div class="logo-icon"><i class="fas fa-landmark" style="color: white;"></i></div>
            <h2 style="font-size: 1.1rem; letter-spacing: 1px;">KMC PORTAL</h2>
        </div>
        <nav>
            <a href="user_dashboard.php" class="nav-item active"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="applications.php" class="nav-item"><i class="fas fa-file-invoice"></i> Applications</a>
            <a href="payments.php" class="nav-item"><i class="fas fa-wallet"></i> Payments</a>
            <a href="notifications.php" class="nav-item"><i class="fas fa-bell"></i> Notifications</a>
            <a href="settings.php" class="nav-item"><i class="fas fa-user-cog"></i> Settings</a>
            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 1rem 0;">
            <a href="log_in.php" class="nav-item" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="content">
        <div class="welcome-header">
            <div>
                <h1>Hello, <?php echo htmlspecialchars($user_name); ?></h1>
                <p style="color: var(--text-muted);">Welcome to the Kabwe Municipal Council Management System.</p>
            </div>
        </div>

        <div class="alert-banner">
            <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
            <span>Your <strong>Property Rates</strong> for 2026 are now available. Please review your balance.</span>
        </div>

        <div class="dashboard-grid">
            <div class="left-col">
                <div class="action-cards">
                    <div class="action-card" onclick="location.href='applications.php'">
                        <i class="fas fa-plus-circle"></i>
                        <span>Apply for Permit</span>
                    </div>
                    <div class="action-card" onclick="location.href='payments.php'">
                        <i class="fas fa-receipt"></i>
                        <span>Pay Bills</span>
                    </div>
                    <div class="action-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Report Fault</span>
                    </div>
                </div>

                <div class="table-container">
                    <div style="padding: 1.5rem; font-weight: 700; border-bottom: 1px solid var(--border);">Recent Service Status</div>
                    <table>
                        <thead>
                            <tr><th>Ref ID</th><th>Service</th><th>Status</th><th>Updated</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>#KMC-992</td><td>Building Permit</td><td><span class="badge badge-success">Approved</span></td><td>Today</td></tr>
                            <tr><td>#KMC-841</td><td>Waste Collection</td><td><span class="badge badge-pending">In Progress</span></td><td>Yesterday</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="summary-box">
                    <h3>Recent Activity</h3>
                    <div class="timeline">
                        <div class="timeline-item">
                            <span class="timeline-date">Today, 10:30 AM</span>
                            <span class="timeline-text">Building Permit #402 was approved by Engineering Dept.</span>
                        </div>
                        <div class="timeline-item">
                            <span class="timeline-date">Yesterday</span>
                            <span class="timeline-text">KMC system synchronized your property records.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="widget">
                    <div class="widget-title">Outstanding Balance <i class="fas fa-info-circle" style="color: #cbd5e1;"></i></div>
                    <div class="amount-due">K 0.00</div>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin: 10px 0 20px;">No immediate payments due.</p>
                    <button class="btn-small" style="width: 100%; background: var(--primary); color: white;" onclick="location.href='payments.php'">View Statement</button>
                </div>

                <div class="widget">
                    <div class="widget-title">Your Profile Info</div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="color: var(--primary);"></i>
                        </div>
                        <div>
                            <p style="font-weight: 700; font-size: 0.9rem;"><?php echo htmlspecialchars($user_role); ?></p>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">Citizen Account</p>
                        </div>
                    </div>
                </div>

                <div class="widget" style="background: var(--sidebar-bg); color: white;">
                    <p style="font-size: 0.85rem; opacity: 0.8; margin-bottom: 10px;">Need Help?</p>
                    <p style="font-weight: 600; margin-bottom: 15px;">Contact Council Support</p>
                    <button class="btn-small" style="width: 100%; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);" onclick="location.href='contacts.php'">Start Chat</button>
                </div>
            </div>
        </div>
    </main>

</body>
</html>