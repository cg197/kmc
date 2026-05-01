<?php
session_start();

//  DATABASE CONNECTION
$host = "localhost";
$db_user = "choolweg_kabwe_council_db";
$db_pass = "gambwe1997";
$db_name = "choolweg_kabwe_council_db";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$message_type = "";

// 2. REGISTRATION LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_submit'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // Validation
    if ($pass !== $confirm_pass) {
        $message = "Passwords do not match!";
        $message_type = "error";
    } else {
        //  email check
        $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $res = $check_email->get_result();

        if ($res->num_rows > 0) {
            $message = "This email is already registered.";
            $message_type = "error";
        } else {

            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $role = 'resident';
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash, phone_number, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $full_name, $email, $hashed_password, $phone, $role);

            if ($stmt->execute()) {
                $message = "Account created successfully! <a href='login.php' style='color:inherit; font-weight:bold;'>Login here</a>";
                $message_type = "success";
            } else {
                $message = "Error: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
        $check_email->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMC Portal — Create Account</title>
    <style>
        :root {
            --primary: #1f7a8c;
            --white: #ffffff;
            --bg-gradient: linear-gradient(135deg, #23a7c1 0%, #0f172a 100%);
        }
        * { box-sizing: border-box; font-family: 'Segoe UI', system-ui, sans-serif; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--bg-gradient); padding: 20px; }

        .reg-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }

        h2 { color: var(--primary); text-align: center; margin-top: 0; }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }
        .error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }

        label { display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 5px; color: #444; }
        input { width: 100%; padding: 10px; margin-bottom: 1rem; border: 1px solid #e2e8f0; border-radius: 6px; }

        .btn-reg {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .footer { text-align: center; margin-top: 1rem; font-size: 0.85rem; }
        .footer a { color: #0b6efd; text-decoration: none; }
    </style>
</head>
<body>

<div class="reg-card">
    <h2>Join KMC Portal</h2>

    <?php if($message != ""): ?>
        <div class="alert <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label>FULL NAME</label>
        <input type="text" name="full_name" placeholder="James Hamweene" required>

        <label>EMAIL ADDRESS</label>
        <input type="email" name="email" placeholder="james@example.com" required>

        <label>PHONE NUMBER (Optional)</label>
        <input type="text" name="phone" placeholder="097...">

        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="••••••••" required>

        <label>CONFIRM PASSWORD</label>
        <input type="password" name="confirm_password" placeholder="••••••••" required>

        <button type="submit" name="register_submit" class="btn-reg">Create Account</button>
    </form>

    <div class="footer">
        Already have an account? <a href="log_in.php">Log in here</a>
    </div>
</div>

</body>
</html>