<?php
session_start();
require_once 'config.php'; 

// 1. Validate Request
if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    die("Unauthorized access: Missing parameters.");
}

$id_from_url = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

// 2. Query file - Changed 'id' to 'doc_id' to match your database column name
$stmt = $pdo->prepare("SELECT file_name, file_path FROM documents 
                       WHERE doc_id = :target_id AND user_id = :user_id AND status = 'Approved'");
$stmt->execute(['target_id' => $id_from_url, 'user_id' => $user_id]);
$doc = $stmt->fetch();

if (!$doc) {
    die("Unauthorized: File not found, access denied, or not yet approved.");
}

// 3. Serve the file
$filepath = $doc['file_path'];

// Check if the path needs the 'uploads/' prefix
if (!file_exists($filepath) && file_exists("uploads/" . basename($filepath))) {
    $filepath = "uploads/" . basename($filepath);
}

if (file_exists($filepath)) {
    // Clear any previous output (prevents corrupted PDFs)
    if (ob_get_level()) ob_end_clean();

    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf'); 
    header('Content-Disposition: attachment; filename="'.basename($doc['file_name']).'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    
    readfile($filepath);
    exit;
} else {
    die("Error: Physical file not found on server.");
}
?>