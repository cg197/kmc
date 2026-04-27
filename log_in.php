<?php
session_start();

// DATABASE CONNECTION
$host = "localhost";
$db_user = "choolweg_kabwe_council_db";
$db_pass = "gambwe1997";
$db_name = "choolweg_kabwe_council_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . htmlspecialchars($conn->connect_error));
}

$error_message = "";

// LOGIN LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['log_in_submit'])) {
    $email = trim($_POST['user']);
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role FROM users WHERE email = ? AND deleted_at IS NULL");
    if (!$stmt) {
        $error_message = "Database error: " . htmlspecialchars($conn->error);
    } else {
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            $error_message = "Database error: " . htmlspecialchars($stmt->error);
        } else {
            $result = $stmt->get_result();
        }
    }
    
    if (!isset($error_message) || $error_message === "") {

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

            
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: user_dashboard.php");
                }
                exit();
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "No active account found with that email.";
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Portal — Login</title>
    <style>
        :root {
            --primary: #1f7a8c;
            --accent: #0b6efd;
            --white: #ffffff;
            --text-muted: #6b7280;
            --bg-gradient: linear-gradient(135deg, #23a7c1 0%, #0f172a 100%);
        }
        * { box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-gradient);
            padding: 1.5rem;
        }

        .login-card {
            background: var(--white);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        .login-card::before {
            content: "";
            display: block;
            height: 4px;
            width: 60px;
            margin: 0 auto 1rem auto;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        h1 { font-size: 1.5rem; color: var(--primary); margin: 0; text-transform: uppercase; }
        p.subtitle { color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem; }

        .error-box {
            background: #fee2e2;
            color: #dc2626;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            border: 1px solid #fecaca;
        }

        form { text-align: left; }
        label { display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 5px; color: #444; }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 1.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            transition: all 0.2s;
        }

        input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(31, 122, 140, 0.1); }

        .btn-group { display: flex; flex-direction: column; gap: 10px; }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-login { background: var(--primary); color: white; }
        .btn-register { background: #f1f5f9; color: #334155; }
        button:hover { opacity: 0.9; }

        .footer-links { margin-top: 1.5rem; font-size: 0.85rem; }
        .footer-links a { color: var(--accent); text-decoration: none; }
	
    </style>
</head>
<body>

    <div class="login-card">
        <h1>KMC Portal</h1>
        <p class="subtitle">Kabwe Municipal Council Management System</p>

        <?php if($error_message != ""): ?>
            <div class="error-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="log_in.php">
            <label for="user">EMAIL ADDRESS</label>
            <input type="email" name="user" id="user" placeholder="e.g. name@example.com" required>

            <label for="password">PASSWORD</label>
            <input type="password" name="password" id="password" placeholder="••••••••" required>

            <div class="btn-group">
                <button type="submit" name="log_in_submit" class="btn-login">Login to Portal</button>
                <button type="button" class="btn-register" onclick="location.href='register.php'"></button>
                <button type="button" class="btn-register" onclick="location.href='register.php'">Create New Account</button>
            </div>
        </form>

        <div class="footer-links">
            <a href="recovery.php">Forgot your password?</a>
        </div>
    </div>

</body>
</html>