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

// 3. Handle CSV Export Logic
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="KMC_Users_'.date('Y-m-d').'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Full Name', 'Email', 'Phone', 'Status', 'Joined Date'));
    $rows = $conn->query("SELECT user_id, full_name, email, phone, status, created_at FROM users");
    while($row = $rows->fetch_assoc()) fputcsv($output, $row);
    fclose($output);
    exit();
}

// 4. Handle Account Status Toggle (Block/Unblock)
if (isset($_GET['toggle_id']) && isset($_GET['current_status'])) {
    $tid = intval($_GET['toggle_id']);
    $new_status = ($_GET['current_status'] == 'active') ? 'blocked' : 'active';
    $conn->query("UPDATE users SET status = '$new_status' WHERE user_id = $tid");
    header("Location: user_managements.php?msg=status_updated");
    exit();
}

// 5. Fetch Data with Search
$search = $_POST['search_term'] ?? '';
$query = "SELECT * FROM users WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' ORDER BY user_id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KMC User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #2563eb; --sidebar: #0f172a; --bg: #f1f5f9; --danger: #ef4444; --success: #10b981; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg); display: flex; margin: 0; min-height: 100vh; }
        
        /* Layout */
        .sidebar { width: 260px; background: var(--sidebar); color: white; padding: 20px; height: 100vh; position: sticky; top: 0; box-sizing: border-box; }
        .main { flex: 1; padding: 40px; }
        
        /* Modern Table Card */
        .card { background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 0; overflow: hidden; }
        .card-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        
        /* Table Styling */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px 25px; text-align: left; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 18px 25px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
        tr:hover { background-color: #fcfdfe; }

        /* Badges */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .active-bg { background: #dcfce7; color: #15803d; }
        .blocked-bg { background: #fee2e2; color: #b91c1c; }

        /* Inputs & Buttons */
        .search-box { padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 10px; width: 300px; }
        .btn-action { padding: 8px; border-radius: 8px; border: none; cursor: pointer; transition: 0.2s; color: white; text-decoration: none; font-size: 12px; margin-right: 5px; }
        .btn-export { background: #6366f1; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; }
        
        .nav-link { display: block; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 10px; margin-bottom: 8px; }
        .nav-link.active { background: var(--primary); color: white; }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2 style="font-size: 1.5rem; margin-bottom: 2rem;">KMC Portal</h2>
    <a href="admin_dashboard.php" class="nav-link"><i class="fas fa-chart-line"></i> &nbsp; Dashboard</a>
    <a href="user_managements.php" class="nav-link active"><i class="fas fa-users"></i> &nbsp; User Management</a>
    <a href="admin_reports.php" class="nav-link"><i class="fas fa-file-invoice"></i> &nbsp; Financial Reports</a>
    <a href="logout.php" class="nav-link" style="color: #f87171; margin-top: 50px;"><i class="fas fa-power-off"></i> &nbsp; Logout</a>
</nav>

<main class="main">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <h1 style="margin: 0; color: #1e293b;">Citizen Management</h1>
            <p style="color: #64748b; margin-top: 5px;">Review and manage all registered council users.</p>
        </div>
        <a href="?export=true" class="btn-export"><i class="fas fa-download"></i> &nbsp; Export to CSV</a>
    </div>

    <div class="card">
        <div class="card-header">
            <form method="POST">
                <input type="text" name="search_term" class="search-box" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
            <span style="color: #94a3b8; font-size: 0.9rem;">Total Records: <?php echo $result->num_rows; ?></span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>UID</th>
                    <th>Full Name</th>
                    <th>Contact Info</th>
                    <th>Account Status</th>
                    <th>Join Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><span style="font-family: monospace; color: #94a3b8;">#<?php echo $user['user_id']; ?></span></td>
                    <td>
                        <div style="font-weight: 700; color: #1e293b;"><?php echo htmlspecialchars($user['full_name'] ?? 'Unknown User'); ?></div>
                        <div style="font-size: 12px; color: #64748b;"><?php echo htmlspecialchars($user['email'] ?? 'No Email'); ?></div>
                    </td>
                    <td>
                        <div style="font-size: 13px;"><i class="fas fa-phone" style="width: 15px; font-size: 11px;"></i> <?php echo htmlspecialchars($user['phone'] ?? 'Unlisted'); ?></div>
                    </td>
                    <td>
                        <?php 
                        $status = strtolower($user['status'] ?? 'active'); 
                        echo "<span class='badge ".($status == 'active' ? 'active-bg' : 'blocked-bg')."'>".strtoupper($status)."</span>";
                        ?>
                    </td>
                    <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="?toggle_id=<?php echo $user['user_id']; ?>&current_status=<?php echo $status; ?>" 
                           class="btn-action" style="background: <?php echo ($status == 'active' ? '#f59e0b' : '#10b981'); ?>;" 
                           title="<?php echo ($status == 'active' ? 'Block User' : 'Unblock User'); ?>">
                            <i class="fas <?php echo ($status == 'active' ? 'fa-ban' : 'fa-check'); ?>"></i>
                        </a>
                        
                        <a href="?delete_id=<?php echo $user['user_id']; ?>" 
                           class="btn-action" style="background: var(--danger);" 
                           onclick="return confirm('Permanent action! Delete this user?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>