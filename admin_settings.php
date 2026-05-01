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

$admin_id = $_SESSION['admin_id'];
$message = "";

// 3. Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // CASE A: Update Profile Info (Email/Name)
    if (isset($_POST['update_profile'])) {
        $email = $_POST['email'];
        $name = $_POST['full_name'];
        
        $stmt = $conn->prepare("UPDATE admins SET email = ?, full_name = ? WHERE admin_id = ?");
        $stmt->bind_param("ssi", $email, $name, $admin_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
        }
    }

    // CASE B: Change Password
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        // Verify current password first
        $res = $conn->query("SELECT password FROM admins WHERE admin_id = $admin_id");
        $admin = $res->fetch_assoc();

        if (password_verify($current_pass, $admin['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
                $stmt->bind_param("si", $hashed_pass, $admin_id);
                $stmt->execute();
                $message = "Password changed successfully!";
            } else {
                $message = "New passwords do not match.";
            }
        } else {
            $message = "Incorrect current password.";
        }
    }
}

// 4. Fetch Current Admin Data
$res = $conn->query("SELECT * FROM admins WHERE id = $admin_id");
$admin_data = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings - KMC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --sidebar: #0f172a; --blue: #2563eb; --bg: #f8fafc; --border: #e2e8f0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); display: flex; margin: 0; }
        .sidebar { width: 260px; background: var(--sidebar); color: white; padding: 20px; height: 100vh; position: sticky; top: 0; }
        .nav-link { display: block; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: var(--blue); color: white; }
        
        .main { flex: 1; padding: 40px; }
        .settings-container { max-width: 800px; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #64748b; margin-bottom: 8px; }
        input { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; box-sizing: border-box; }
        
        .btn { background: var(--blue); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; background: #dcfce7; color: #166534; border-left: 5px solid #10b981; }
    </style>
</head>
<body>

<nav class="sidebar">
    <h2>KMC ADMIN</h2><br>
    <a href="admin_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a>
    <a href="admin_settings.php" class="nav-link active"><i class="fas fa-cog"></i> Settings</a>
    <a href="admin_login.php" class="nav-link" style="color:#ef4444; margin-top: 40px;"><i class="fas fa-power-off"></i> Logout</a>
</nav>

<main class="main">
    <div class="settings-container">
        <h1>Account Settings</h1>

        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3><i class="fas fa-user-circle"></i> Profile Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin_data['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn">Save Changes</button>
            </form>
        </div>

        <div class="card">
            <h3><i class="fas fa-lock"></i> Security</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn" style="background:#0f172a;">Update Password</button>
            </form>
        </div>

        <div class="card">
            <h3><i class="fas fa-sliders-h"></i> System Preferences</h3>
            <p style="color:#64748b; font-size: 14px;">Toggle system-wide maintenance mode or update application fees.</p>
            <div class="form-group">
                <label>Standard Permit Fee (ZMW)</label>
                <input type="number" value="150" enabled>
                <small style="color:#94a3b8;">Contact Database Admin to modify global fee constants.</small>
            </div>
        </div>
    </div>
</main>

</body>
</html>