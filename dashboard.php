<?php
require_once __DIR__ . '/config/init.php';
 
// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: auth/login.php");
    exit;
}
require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="jumbotron text-center">
                <h1 class="display-4">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
                <p class="lead">This is your dashboard. From here you can manage your culinary posts.</p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">What would you like to do?</h5>
                    <p class="card-text">Create a new post or manage your existing posts.</p>
                    <a href="auth/create.php" class="btn btn-primary">Create a New Post</a>
                    <a href="auth/posts.php" class="btn btn-secondary">View & Manage My Posts</a>
                </div>
            </div>
        </div>

        <?php if ($_SESSION["role"] === 'admin'): ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Admin Actions</h5>
                    <p class="card-text">As an admin, you can manage all site content.</p>
                    <a href="admin/index.php" class="btn btn-danger">Go to Admin Panel</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
