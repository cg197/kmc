<?php
session_start();
require_once 'config.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC | Administrative Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --dark: #0f172a;
            --slate: #64748b;
            --glass: rgba(255, 255, 255, 0.9);
        }

        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .login-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Left Side */
        .brand-side {
            flex: 1.2;
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .brand-side::before {
            content: "";
            position: absolute;
            width: 500px; height: 500px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.15;
            top: -100px; left: -100px;
        }

        .brand-content { position: relative; z-index: 2; text-align: center; max-width: 450px; }
        .brand-content i { font-size: 4rem; margin-bottom: 20px; color: var(--primary); }
        .brand-content h1 { font-size: 2.5rem; margin: 0 0 15px 0; font-weight: 800; }
        .brand-content p { font-size: 1.1rem; color: #94a3b8; line-height: 1.6; }

        /*  Login Form */
        .form-side {
            flex: 1;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 { font-size: 1.8rem; font-weight: 800; color: var(--dark); margin-bottom: 10px; }
        .login-card p { color: var(--slate); margin-bottom: 30px; }

        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--dark); }

        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute; left: 15px; top: 50%;
            transform: translateY(-50%); color: var(--slate);
        }

        .form-group input {
            width: 100%; padding: 12px 12px 12px 45px;
            border: 1px solid #e2e8f0; border-radius: 12px;
            font-size: 1rem; transition: 0.2s; background: #fcfdfe;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: white;
        }

        .btn-login {
            width: 100%; padding: 14px;
            background: var(--primary); color: white;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 700; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .error-box {
            background: #fff1f2; color: #be123c;
            padding: 12px; border-radius: 10px;
            font-size: 0.9rem; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            border: 1px solid #ffe4e6;
        }

        .footer-text {
            text-align: center; margin-top: 30px;
            font-size: 0.8rem; color: var(--slate);
        }

        @media (max-width: 900px) {
            .brand-side { display: none; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="brand-side">
        <div class="brand-content">
            <i class="fas fa-landmark"></i>
            <h1>KMC Portal</h1>
            <p>Administrative Management System for the Kabwe Municipal Council. Secure access for authorized personnel only.</p>
        </div>
    </div>

    <div class="form-side">
        <div class="login-card">
            <h2>Welcome back</h2>
            <p>Please enter your administrative credentials.</p>

            <?php if($error): ?>
                <div class="error-box">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Enter admin username" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Sign In to Console
                </button>
            </form>

            <div class="footer-text">
                &copy; 2026 Kabwe Municipal Council. <br> ICT Department. All Rights Reserved.
            </div>
        </div>
    </div>
</div>

</body>
</html>