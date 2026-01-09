<?php
require_once __DIR__ . '/../config/init.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

// Check for post ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: ../index.php");
    exit;
}

$post_id = $_GET['id'];
$title = $content = $current_image = "";
$title_err = $content_err = $image_err = "";

// Fetch post data to check ownership and fill form
$sql_fetch = "SELECT user_id, title, content, image FROM posts WHERE id = ?";
if ($stmt_fetch = $mysqli->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $post_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($result->num_rows == 1) {
        $post = $result->fetch_assoc();
        // Security check: user must be author or admin
        if ($_SESSION['id'] != $post['user_id'] && $_SESSION['role'] != 'admin') {
            header("location: ../index.php"); // Redirect if not authorized
            exit;
        }
        $title = $post['title'];
        $content = $post['content'];
        $current_image = $post['image'];
    } else {
        header("location: ../index.php"); // Post not found
        exit;
    }
    $stmt_fetch->close();
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter content.";
    } else {
        $content = trim($_POST["content"]);
    }

    $new_image_name = $current_image;
    // Handle new image upload
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Validation logic from create.php
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["image"]["name"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $image_err = "Invalid file format.";
        } else {
            $new_image_name = uniqid() . "." . $ext;
            $upload_path = '../uploads/' . $new_image_name;
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)) {
                // Delete old image if it exists
                if (!empty($current_image) && file_exists('../uploads/' . $current_image)) {
                    unlink('../uploads/' . $current_image);
                }
            } else {
                $image_err = "Failed to upload new image.";
                $new_image_name = $current_image; // Revert to old image on failure
            }
        }
    }

    // Update database if no errors
    if (empty($title_err) && empty($content_err) && empty($image_err)) {
        $sql_update = "UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?";
        if ($stmt_update = $mysqli->prepare($sql_update)) {
            $stmt_update->bind_param("sssi", $title, $content, $new_image_name, $post_id);
            if ($stmt_update->execute()) {
                header("location: ../posts/view.php?id=" . $post_id);
                exit();
            } else {
                echo "Something went wrong. Please try again.";
            }
            $stmt_update->close();
        }
    }
    $mysqli->close();
}
require_once '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-body">
                    <h2 class="card-title text-center">Edit Post</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $post_id; ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($title); ?>">
                            <div class="invalid-feedback"><?php echo $title_err; ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" id="content" class="form-control <?php echo (!empty($content_err)) ? 'is-invalid' : ''; ?>"><?php echo htmlspecialchars($content); ?></textarea>
                            <div class="invalid-feedback"><?php echo $content_err; ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <?php if (!empty($current_image)): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($current_image); ?>" class="img-fluid rounded" style="max-width: 200px;">
                                <?php else: ?>
                                    <p>No image uploaded.</p>
                                <?php endif; ?>
                            </div>
                            <label for="image" class="form-label mt-3">Upload New Image (optional)</label>
                            <input type="file" name="image" id="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                            <div class="invalid-feedback"><?php echo $image_err; ?></div>
                        </div>
                        <div class="d-grid gap-2">
                            <input type="submit" class="btn btn-primary" value="Save Changes">
                            <a href="../posts/view.php?id=<?php echo $post_id; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
