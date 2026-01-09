<?php
require_once __DIR__ . '/../config/init.php';





if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $user_id = $_SESSION['id'];
    if ($_SESSION['role'] === 'admin') {
        $sql = "SELECT posts.id, posts.title, posts.created_at, posts.updated_at, users.username, posts.user_id FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
    } else {
        $sql = "SELECT posts.id, posts.title, posts.created_at, posts.updated_at, users.username, posts.user_id FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id = ? ORDER BY posts.created_at DESC";
    }
} else {
    $sql = "SELECT posts.id, posts.title, posts.created_at, posts.updated_at, users.username, posts.user_id FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC";
}
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2><?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                        if ($_SESSION['role'] === 'admin') {
                            echo 'All Posts';
                        } else {
                            echo 'My Posts';
                        }
                    } else {
                        echo 'All Posts';
                    } ?></h2>
                </div>
                <div class="card-body">
                    <p>Here you can view, edit, or delete posts.</p>

<?php
                    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                        if ($_SESSION['role'] === 'admin') {
                            $result = $mysqli->query($sql);
                        } else {
                            $stmt = $mysqli->prepare($sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        }
                    } else {
                        $result = $mysqli->query($sql);
                    }

                        if ($result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Created</th>
                                            <th>Last Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($post = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($post['title']); ?></td>
                                                <td><?php echo htmlspecialchars($post['username']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($post['updated_at'])); ?></td>
                                                <td>
                                                    <a href="../admin/view.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-info">View</a>
                                                    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                                                        <?php if ($_SESSION['id'] == $post['user_id'] || $_SESSION['role'] == 'admin'): ?>
                                                            <a href="../admin/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                            <a href="../admin/delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p><?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                                    if ($_SESSION['role'] === 'admin') {
                                        echo 'No posts found.';
                                    } else {
                                        echo 'You haven\'t created any posts yet. <a href="create.php">Why not create one now?</a>';
                                    }
                                } else {
                                    echo 'No posts found.';
                                } ?></p>
                            </div>
                        <?php endif;
                        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION['role'] !== 'admin') {
                            $stmt->close();
                        }

                    $mysqli->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
