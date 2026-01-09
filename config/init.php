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