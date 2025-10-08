<?php 
include '../db.php'; 
if (!isLoggedIn('user')) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$orders_sql = "SELECT o.*, t.name, t.price, u.username as cook_name FROM orders o 
               JOIN tiffins t ON o.tiffin_id = t.tiffin_id 
               JOIN users u ON t.cook_id = u.user_id 
               WHERE o.user_id = $user_id ORDER BY o.order_date DESC";
$orders = $conn->query($orders_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Order History</h1>
        <p><a style="color: white;" href="dashboard.php">Back to Dashboard</a> | <a style="color: white;" href="../logout.php">Logout</a></p>
    </header>
    <main>
        <section>
            <?php if ($orders->num_rows > 0): ?>
                <ul class="order-list">
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <li class="order-item">
                            <h3><?php echo htmlspecialchars($order['name']); ?> x<?php echo $order['quantity']; ?></h3>
                            <p>Total: $<?php echo ($order['price'] * $order['quantity']); ?></p>
                            <p>From: <?php echo htmlspecialchars($order['cook_name']); ?></p>
                            <p>Date: <?php echo $order['order_date']; ?></p>
                            <p>Status: <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No orders yet. <a href="dashboard.php">Browse tiffins</a> to place one!</p>
            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/js/script.js"></script>
</body>
</html>