<?php
require_once __DIR__ . '/../config/init.php';

// Security check
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'admin') {
    header("location: ../index.php");
    exit;
}

// Handle role change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_role'])) {
    $user_id_to_change = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Admins cannot change their own role to prevent lockout
    if ($user_id_to_change != $_SESSION['id']) {
        $sql_update_role = "UPDATE users SET role = ? WHERE id = ?";
        if ($stmt = $mysqli->prepare($sql_update_role)) {
            $stmt->bind_param("si", $new_role, $user_id_to_change);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id'];

    // Admins cannot delete themselves
    if ($user_id_to_delete != $_SESSION['id']) {
        // The ON DELETE CASCADE in the DB schema will handle deleting the user's posts
        $sql_delete_user = "DELETE FROM users WHERE id = ?";
        if ($stmt = $mysqli->prepare($sql_delete_user)) {
            $stmt->bind_param("i", $user_id_to_delete);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch all users
$sql_fetch_users = "SELECT id, username, role, created_at FROM users ORDER BY created_at DESC";
$users_result = $mysqli->query($sql_fetch_users);
require_once '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Manage Users</h2>
                </div>
                <div class="card-body">
                    <p>Here you can change user roles or delete users entirely.</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users_result->num_rows > 0):
                                    while ($user = $users_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <?php if ($user['id'] != $_SESSION['id']): // Prevent admin from editing self in this simple form ?>
                                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline-block">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <div class="input-group">
                                                            <select name="role" class="form-select">
                                                                <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>User</option>
                                                                <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                                            </select>
                                                            <button type="submit" name="change_role" class="btn btn-sm btn-primary">Save</button>
                                                        </div>
                                                    </form>
                                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="d-inline-block">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will delete the user and all their posts.');">Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">(Your Account)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$users_result->free();
$mysqli->close();
require_once '../includes/footer.php';
?>
