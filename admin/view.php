<?php
require_once __DIR__ . '/../config/init.php';

// Check for post ID in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: ../index.php");
    exit;
}

$post_id = $_GET['id'];

// Fetch the post from the database
$sql = "SELECT posts.id, posts.title, posts.content, posts.image, posts.created_at, posts.user_id, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $post_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $post = $result->fetch_assoc();
        } else {
            // Post not found, redirect
            header("location: ../index.php");
            exit;
        }
    } else {
        echo "Oops! Something went wrong.";
        exit;
    }
    $stmt->close();
}
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <?php if (!empty($post['image'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <p class="card-subtitle mb-2 text-muted">
                        Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                    </p>
                    <div class="card-text">
                        <?php echo nl2br(htmlspecialchars($post['content'])); // nl2br to respect new lines ?>
                    </div>
                </div>
                <div class="card-footer">
                    <?php
                    // Show edit/delete buttons if user is the author or an admin
                    if (isset($_SESSION['loggedin']) && ($_SESSION['id'] == $post['user_id'] || $_SESSION['role'] == 'admin')) {
                        echo '<a href="edit.php?id=' . $post['id'] . '" class="btn btn-primary">Edit</a> ';
                        echo '<a href="delete.php?id=' . $post['id'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this post?\');">Delete</a>';
                    }
                    ?>
                    <a href="../index.php" class="btn btn-secondary">Back to All Posts</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$mysqli->close();
require_once '../includes/footer.php';
?>
