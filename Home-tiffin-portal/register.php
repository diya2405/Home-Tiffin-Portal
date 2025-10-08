<?php 
include 'db.php'; 
$error = ''; $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    $check_sql = "SELECT user_id FROM users WHERE username = '$username'";
    if ($conn->query($check_sql)->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        $sql = "INSERT INTO users (username, password, role, location) VALUES ('$username', '$password', '$role', '$location')";
        if ($conn->query($sql)) {
            $success = "Registered successfully! Please login.";
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tiffin Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label>
            <label>Password: <input type="password" name="password" required></label>
            <label>Role: 
                <select name="role" required>
                    <option value="user">User  (Customer)</option>
                    <option value="cook">Cook (Provider)</option>
                </select>
            </label>
            <label>Location: <input type="text" name="location" placeholder="e.g., Mumbai" required></label>
            <button type="submit" class="btn">Register</button>
        </form>
        <p><a href="login.php">Already have an account? Login</a></p>
        <a href="index.php">Back to Home</a>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>