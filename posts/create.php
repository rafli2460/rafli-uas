<?php
require_once '../includes/header.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth/login.php");
    exit;
}

$title = $content = "";
$title_err = $content_err = $image_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter the content for your post.";
    } else {
        $content = trim($_POST["content"]);
    }

    // Validate and process image upload
    $image_name = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            $image_err = "Please select a valid file format (JPG, PNG, GIF).";
        }

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $image_err = "File size is larger than the allowed limit of 5MB.";
        }

        // Verify MIME type of the file
        if (in_array($filetype, $allowed)) {
            // Generate a unique name for the file before saving it
            $image_name = uniqid() . "." . $ext;
            $upload_path = '../uploads/' . $image_name;

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)) {
                $image_err = "Failed to upload the image. Please try again.";
                $image_name = ""; // Reset on failure
            }
        } else {
            $image_err = "There was a problem with your file upload.";
        }
    }

    // Check input errors before inserting in database
    if (empty($title_err) && empty($content_err) && empty($image_err)) {
        $sql = "INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("isss", $_SESSION["id"], $title, $content, $image_name);

            if ($stmt->execute()) {
                // Redirect to dashboard after successful post creation
                header("location: ../dashboard.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $mysqli->close();
}
?>

<div class="form-wrapper">
    <h2>Create New Post</h2>
    <p>Share your culinary masterpiece with the world!</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
            <span class="invalid-feedback"><?php echo $title_err; ?></span>
        </div>
        <div class="form-group">
            <label>Content</label>
            <textarea name="content" class="form-control"><?php echo $content; ?></textarea>
            <span class="invalid-feedback"><?php echo $content_err; ?></span>
        </div>
        <div class="form-group">
            <label>Featured Image</label>
            <input type="file" name="image" class="form-control">
            <span class="invalid-feedback"><?php echo $image_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Publish Post">
            <a href="../dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
