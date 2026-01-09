<?php
require_once '../includes/header.php';

// Security check: ensure user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php");
    exit;
}
?>

<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>. You have full control.</p>
    
    <div class="dashboard-actions">
        <h2>Admin Controls</h2>
        <p>From here, you can manage all users and their posts.</p>
        <ul>
            <li><a href="manage_posts.php" class="btn btn-primary">Manage All Posts</a></li>
            <li><a href="manage_users.php" class="btn btn-danger">Manage Users</a></li>
        </ul>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
