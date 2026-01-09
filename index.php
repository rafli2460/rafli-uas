<?php require_once __DIR__ . '/config/init.php'; 
 require_once 'includes/header.php'; ?>

<div class="container mt-5">
    <div class="jumbotron text-center">
        <h1 class="display-4">Latest Culinary Posts</h1>
        <p class="lead">Discover the most delicious recipes and culinary stories from our community.</p>
    </div>

    <div class="row">
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
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($post['image'])): ?>
                            <a href="admin/view.php?id=<?php echo $post['id']; ?>">
                                <img src="uploads/<?php echo htmlspecialchars($post['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </a>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><a href="admin/view.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h5>
                            <p class="card-subtitle mb-2 text-muted">
                                By <?php echo htmlspecialchars($post['username']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            </p>
                            <p class="card-text">
                                <?php
                                // Show a truncated version of the content
                                $content_preview = strip_tags($post['content']);
                                if (strlen($content_preview) > 100) {
                                    $content_preview = substr($content_preview, 0, 100) . '...';
                                }
                                echo $content_preview;
                                ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="admin/view.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
        else:
        ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <p>No posts have been made yet. Be the first!</p>
                </div>
            </div>
        <?php
        endif;
        
        // Close the connection
        $mysqli->close();
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
