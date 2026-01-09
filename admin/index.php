<?php
require_once __DIR__ . '/../config/init.php';

// Security check: ensure user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php");
    exit;
}
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="jumbotron text-center">
                <h1 class="display-4">Admin Dashboard</h1>
                <p class="lead">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>. You have full control.</p>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Posts</h5>
                    <p class="card-text">View, edit, or delete any post on the site.</p>
                    <a href="create.php" class="btn btn-primary">Create a New Post</a>
                    <a href="posts.php" class="btn btn-primary">Manage All Posts</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">View, edit, or delete user accounts.</p>
                    <a href="users.php" class="btn btn-danger">Manage Users</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
