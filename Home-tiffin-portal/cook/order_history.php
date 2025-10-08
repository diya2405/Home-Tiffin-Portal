<?php
session_start();
include('../db.php'); // Fixed include path to go up one level

// Only allow users
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user'){
    header("Location: ../login.php"); // Fixed redirect path
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders placed by this user - Using prepared statement
$sql_orders = "
    SELECT o.*, t.name AS menu, t.quantity, u.username AS cook_name
    FROM orders o
    JOIN tiffins t ON o.tiffin_id = t.tiffin_id
    JOIN users u ON t.cook_id = u.user_id
    WHERE o.user_id = ?
    ORDER BY o.order_id DESC
"; // NOTE: Assumed 'menu' and 'quantity' columns in your original user/order_history.php refer to tiffin name and order quantity. Adjusted the join.

$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders_query = $stmt_orders->get_result();

// ... (Rest of HTML is fine, only minor fixes in the include/redirect paths)
?>
<!DOCTYPE html>
<html>
<body>
<header>
    <div class="nav">
        <h1>Home Tiffin Service - My Orders</h1>
        <div class="links">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span> |
            <a href="dashboard.php">Dashboard</a> |             <a href="../logout.php">Logout</a>         </div>
    </div>
</header>

<main class="container">
    <h2>My Orders</h2>

    <?php if($orders_query->num_rows > 0){ ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Cook</th>
                <th>Menu / Dish</th>
                <th>Quantity</th>
                <th>Status</th>
            </tr>
            <?php while($order = $orders_query->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo htmlspecialchars($order['cook_name']); ?></td>
                <td><?php echo htmlspecialchars($order['menu']); ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td><?php echo ucfirst($order['status']); ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>You have not placed any orders yet.</p>
    <?php } ?>
</main>
</body>
</html>
<?php $stmt_orders->close(); ?>