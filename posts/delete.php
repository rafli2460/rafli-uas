<?php
require_once '../config/database.php'; // We only need db connection and session
session_start();

// If user is not logged in, redirect
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// Check for post ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: ../index.php");
    exit;
}

$post_id = $_GET['id'];

// Fetch post to check ownership and get image filename
$sql_fetch = "SELECT user_id, image FROM posts WHERE id = ?";
if ($stmt_fetch = $mysqli->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $post_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();

    if ($result->num_rows == 1) {
        $post = $result->fetch_assoc();

        // Security check: user must be author or admin
        if ($_SESSION['id'] == $post['user_id'] || $_SESSION['role'] == 'admin') {
            
            // First, delete the image file from the server
            if (!empty($post['image']) && file_exists('../uploads/' . $post['image'])) {
                unlink('../uploads/' . $post['image']);
            }

            // Now, delete the post from the database
            $sql_delete = "DELETE FROM posts WHERE id = ?";
            if ($stmt_delete = $mysqli->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $post_id);
                if ($stmt_delete->execute()) {
                    // Redirect to home page after deletion
                    header("location: ../index.php");
                    exit();
                } else {
                    echo "Error deleting record.";
                }
                $stmt_delete->close();
            }
        } else {
            // Not authorized
            header("location: ../index.php");
            exit;
        }
    } else {
        // Post not found
        header("location: ../index.php");
        exit;
    }
    $stmt_fetch->close();
}

$mysqli->close();
?>
