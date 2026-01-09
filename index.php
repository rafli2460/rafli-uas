<?php require_once 'includes/header.php'; ?>

<h1>Latest Culinary Posts</h1>

<div class="posts-container">
    <?php
    // Fetch all posts from the database, joining with users to get author's name
    $sql = "SELECT posts.id, posts.title, posts.content, posts.image, posts.created_at, users.username 
            FROM posts 
            JOIN users ON posts.user_id = users.id 
            ORDER BY posts.created_at DESC";
    
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0):
        // Loop through each post
        while($post = $result->fetch_assoc()):
    ?>
            <div class="post-card">
                <h2><a href="posts/view.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <div class="post-meta">
                    Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                </div>
                <?php if (!empty($post['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <?php endif; ?>
                <div class="post-content">
                    <?php
                    // Show a truncated version of the content
                    $content_preview = strip_tags($post['content']);
                    if (strlen($content_preview) > 200) {
                        $content_preview = substr($content_preview, 0, 200) . '...';
                    }
                    echo $content_preview;
                    ?>
                </div>
                <a href="posts/view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
            </div>
    <?php
        endwhile;
    else:
        echo "<p>No posts have been made yet. Be the first!</p>";
    endif;
    
    // Close the connection
    $mysqli->close();
    ?>
</div>

<?php require_once 'includes/footer.php'; ?>
