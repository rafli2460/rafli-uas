<?php
// Start the session on every page
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the database configuration
require_once __DIR__ . '/../config/database.php';

// Define the base path for assets
$base_url = ''; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culinary Blog</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container">
        <a href="<?php echo $base_url; ?>/index.php" class="logo">Culinary Blog</a>
        <nav class="nav">
            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <a href="<?php echo $base_url; ?>/dashboard.php">Dashboard</a>
                <?php if ($_SESSION["role"] === 'admin'): ?>
                    <a href="<?php echo $base_url; ?>/admin/index.php">Admin Panel</a>
                <?php endif; ?>
                <a href="<?php echo $base_url; ?>/auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>/auth/login.php">Login</a>
                <a href="<?php echo $base_url; ?>/auth/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
