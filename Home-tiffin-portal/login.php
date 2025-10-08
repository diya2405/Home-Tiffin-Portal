        




<?php 
include 'db.php'; 
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT user_id, username, password, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            redirect( ($user['role'] == 'cook') ? 'cook/dashboard.php' : 'user/dashboard.php' );
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User  not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tiffin Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label>
            <label>Password: <input type="password" name="password" required></label>
            <button type="submit" class="btn">Login</button>
        </form>
        <p><a href="register.php">Don't have an account? Register</a></p>
        <a href="index.php">Back to Home</a>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>