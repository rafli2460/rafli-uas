<?php
require_once __DIR__ . '/../config/init.php';

$username = "";
$password = "";
$username_err = "";
$password_err = "";
$register_err = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);
            $param_username = trim($_POST["username"]);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                $register_err = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($register_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ss", $param_username, $param_password);
            
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            if ($stmt->execute()) {
                $success_msg = "Registration successful! You can now <a href='login.php'>login</a>.";
                // Clear form fields
                $username = "";
                $password = "";
            } else {
                $register_err = "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    
    $mysqli->close();
}
require_once '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-body">
                    <h2 class="card-title text-center">Register</h2>
                    <p class="card-text text-center">Please fill this form to create an account.</p>

                    <?php 
                    if(!empty($register_err)){
                        echo '<div class="alert alert-danger">' . $register_err . '</div>';
                    }        
                    if(!empty($success_msg)){
                        echo '<div class="alert alert-success">' . $success_msg . '</div>';
                    }
                    ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                            <div class="invalid-feedback"><?php echo $username_err; ?></div>
                        </div>    
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                            <div class="invalid-feedback"><?php echo $password_err; ?></div>
                        </div>
                        <div class="d-grid">
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a>.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
