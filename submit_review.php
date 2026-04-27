<?php
session_start();
include 'db_connect.php';

// 1. Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit a review.");
}

// 2. Check if the form was actually submitted
if (isset($_POST['submit_review'])) {
    
    // 3. Sanitize and validate inputs
    $user_id = $_SESSION['user_id'];
    
    // Ensure rating is an integer between 1 and 5
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);
    
    // cleans and prevent XSS and SQL injection
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // Basic validation check
    if ($rating < 1 || $rating > 5 || empty($comment)) {
        header("Location: user_dashboard.php?error=InvalidInput");
        exit();
    }

    // 4. Prepare the SQL Query
    $query = "INSERT INTO reviews (user_id, rating, comment, created_at) 
              VALUES ('$user_id', '$rating', '$comment', NOW())";

    // 5. Execute and Redirect
    if (mysqli_query($conn, $query)) {
        // Redirect back to dashboard with a success message
        header("Location: user_dashboard.php?status=success");
        exit();
    } else {
        // Handle database errors
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If someone tries to access this file directly without posting the form
    header("Location: user_dashboard.php");
    exit();
}
?>