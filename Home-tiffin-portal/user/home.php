<?php
session_start();

// Redirect logged-in users directly to their dashboard
if(isset($_SESSION['user_id'])){
    if($_SESSION['role'] == 'user'){
        header("Location: user_dashboard.php");
        exit();
    } elseif($_SESSION['role'] == 'cook'){
        header("Location: cook_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Tiffin Service</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<header>
    <div class="nav">
        <h1>Home Tiffin Service</h1>
        <div class="links">
            <a href="login.php">Login</a> |
            <a href="register.php">Register</a>
        </div>
    </div>
</header>

<main class="container">
    <section class="hero">
        <h2>Delicious Home-Cooked Meals Delivered to You!</h2>
        <p>Find nearby home cooks and order freshly prepared tiffins.</p>
        <a href="register.php" class="btn">Get Started</a>
    </section>

    <section class="features">
        <h3>How It Works</h3>
        <div class="feature-cards">
            <div class="card">
                <h4>1. Register</h4>
                <p>Create an account as a user or home cook.</p>
            </div>
            <div class="card">
                <h4>2. Browse</h4>
                <p>Explore nearby cooks and their menu items.</p>
            </div>
            <div class="card">
                <h4>3. Order</h4>
                <p>Place your order and enjoy fresh home-cooked meals.</p>
            </div>
        </div>
    </section>
</main>
</body>
</html>
