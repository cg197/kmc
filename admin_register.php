<?php
require_once 'config.php';
$message = "";
$status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user, $email, $pass, $role])) {
            $message = "Account created successfully!";
            $status = "success";
        }
 } catch (PDOException $e) {
    // This will tell you the ACTUAL database error
    $message = "Database Error: " . $e->getMessage();
    $status = "error";
}   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC | Create Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --dark: #0f172a;
            --bg: #f8fafc;
            --success: #10b981;
            --error: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .reg-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .icon-header {
            width: 60px; height: 60px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 20px;
        }

        h2 { margin: 0 0 10px; color: var(--dark); font-weight: 700; }
        p { color: #64748b; margin-bottom: 30px; font-size: 0.9rem; }

        .form-group { text-align: left; margin-bottom: 18px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--dark); }

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            box-sizing: border-box;
            background: #fcfdfe;
            transition: 0.2s;
        }

        input:focus, select:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: #fff;
        }

        .btn-reg {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-reg:hover { background: #1d4ed8; transform: translateY(-1px); }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        .back-link { margin-top: 20px; display: block; font-size: 0.85rem; color: var(--primary); text-decoration: none; }
    </style>
</head>
<body>

<div class="reg-card">
    <div class="icon-header"><i class="fas fa-user-shield"></i></div>
    <h2>Admin Registration</h2>
    <p>Create a new authorized personnel account.</p>

    <?php if($message): ?>
        <div class="alert alert-<?php echo $status; ?>">
            <i class="fas <?php echo $status == 'success' ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="e.g. officer_musonda" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="name@kmc.gov.zm" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label>Access Level</label>
            <select name="role">
                <option value="Officer">Council Officer</option>
                <option value="Finance">Finance Dept</option>
                <option value="SuperAdmin">Super Admin</option>
            </select>
        </div>

        <button type="submit" class="btn-reg">Create Admin Account</button>
    </form>

    <a href="admin_login.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
</div>

</body>
</html>