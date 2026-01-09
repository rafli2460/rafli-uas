<?php
require_once '../includes/header.php';

// Security check: ensure user is logged in and is an admin
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php");
    exit;
}

// Fetch all posts from all users
$sql = "SELECT posts.id, posts.title, posts.created_at, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC";
?>

<div class="admin-wrapper">
    <h2>Manage All Posts</h2>
    <p>Here you can view, edit, or delete any post on the website.</p>

    <?php if ($result = $mysqli->query($sql)):
        if ($result->num_rows > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="../posts/view.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary">View</a>
                                <a href="../posts/edit.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Edit</a>
                                <a href="../posts/delete.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>There are no posts on the site yet.</p>
        <?php endif;
        $result->free();
    endif;
    $mysqli->close();
    ?>
</div>

<?php require_once '../includes/footer.php'; ?>
