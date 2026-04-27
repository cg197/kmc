<?php

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'choolweg_kabwe_council_db');
define('DB_USER', 'choolweg_kabwe_council_db');
define('DB_PASS', 'gambwe1997');

// 2. Application Constants
define('BASE_URL', 'http://localhost/kmc_portal/'); 

// 3. Database Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // Log the error securely instead of echoing it in production
    error_log($e->getMessage());
    die("Database connection failed. Please check your configuration.");
}

// 4. Session Security Settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}
?>