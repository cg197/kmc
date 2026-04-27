<?php
session_start();
require_once 'config.php';

// Security Check
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access.");
}

if (isset($_GET['id'])) {
    $request_id = intval($_GET['id']);

    // Fetch the file path from the database
    $stmt = $pdo->prepare("SELECT file_path, file_name, doc_type FROM documents WHERE request_id = ? LIMIT 1");
    $stmt->execute([$request_id]);
    $file = $stmt->fetch();

    if ($file && file_exists($file['file_path'])) {
        $path = $file['file_path'];
        $mime = mime_content_type($path);

        //  file  display
        header("Content-Type: " . $mime);
        header("Content-Disposition: inline; filename=\"" . $file['file_name'] . "\"");
        readfile($path);
        exit;
    } else {
        die("File not found or record missing.");
    }
}
?>