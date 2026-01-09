<?php
require_once 'includes/header.php';
 
// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: auth/login.php");
    exit;
}
?>

<div class="dashboard-welcome">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
    <p>This is your dashboard. From here you can manage your culinary posts.</p>
</div>

<div class="dashboard-actions">
    <h2>What would you like to do?</h2>
    <ul>
        <li><a href="posts/create.php" class="btn btn-primary">Create a New Post</a></li>
        <li><a href="posts/myposts.php" class="btn btn-secondary">View & Manage My Posts</a></li>
    </ul>

    <?php if ($_SESSION["role"] === 'admin'): ?>
    <div class="admin-section" style="margin-top: 40px;">
        <h2>Admin Actions</h2>
        <p>As an admin, you can manage all site content.</p>
        <ul>
            <li><a href="admin/index.php" class="btn btn-danger">Go to Admin Panel</a></li>
        </ul>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
