<?php
session_start();
require_once 'config.php'; //  PDO connection

// 1. Validate Request
if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$doc_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// 2. Query file
$stmt = $pdo->prepare("SELECT file_name, file_path FROM documents 
                       WHERE doc_id = :doc_id AND user_id = :user_id AND status = 'Approved'");
$stmt->execute(['doc_id' => $doc_id, 'user_id' => $user_id]);
$doc = $stmt->fetch();

if (!$doc) {
    die("File not found or not yet approved for download.");
}

// 3. Serve the file
$filepath = $doc['file_path'];

if (file_exists($filepath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    die("File does not exist on server.");
}
?>