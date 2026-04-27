<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .recovery-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 { color: #333; }
        p { color: #666; font-size: 0.9rem; margin-bottom: 1.5rem; }
        input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        button:hover { background-color: #0056b3; }
        .back-link {
            display: block;
            margin-top: 1rem;
            text-decoration: none;
            color: #007bff;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="recovery-container">
    <h2>Forgot Password?</h2>
    <p>Enter your email address below and we'll send you a link to reset your password.</p>

    <form action="/send-recovery-email" method="POST">
        <input type="email" name="email" placeholder="name@example.com" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <a href="log_in.php" class="back-link">Back to Login</a>
</div>

</body>
</html>