<?php
require_once '../includes/header.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Fetch all posts for the current user
$sql = "SELECT id, title, created_at, updated_at FROM posts WHERE user_id = ? ORDER BY created_at DESC";

?>

<div class="myposts-wrapper">
    <h2>My Posts</h2>
    <p>Here you can view, edit, or delete the posts you have created.</p>

    <?php if ($stmt = $mysqli->prepare($sql)):
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Created</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($post['updated_at'])); ?></td>
                            <td>
                                <a href="view.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary">View</a>
                                <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <p>You haven't created any posts yet. <a href="create.php">Why not create one now?</a></p>
            </div>
        <?php endif;
        $stmt->close();
    endif;
    $mysqli->close();
    ?>
</div>

<?php require_once '../includes/footer.php'; ?>
