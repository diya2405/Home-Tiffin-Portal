<?php 
include '../db.php'; 
if (!isLoggedIn('cook')) { redirect('../login.php'); }

$cook_id = $_SESSION['user_id'];

// Handle order acceptance (POST)
$success_msg = ''; $error_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_order'])) {
    $order_id = intval($_POST['order_id']);
    if ($order_id > 0) {
        // Verify it's this cook's order and pending
        $check_sql = "SELECT o.status FROM orders o JOIN tiffins t ON o.tiffin_id = t.tiffin_id WHERE o.order_id = $order_id AND t.cook_id = $cook_id";
        $check_result = $conn->query($check_sql);
        if ($check_result && $check_result->num_rows > 0 && $check_result->fetch_assoc()['status'] == 'pending') {
            $update_sql = "UPDATE orders SET status = 'completed' WHERE order_id = $order_id";
            if ($conn->query($update_sql)) {
                $success_msg = "Order accepted successfully! The user will see this update in their history.";
            } else {
                $error_msg = "Failed to accept order: " . $conn->error;
            }
        } else {
            $error_msg = "Invalid order or already accepted.";
        }
    }
}

// Handle tiffin deletion redirect (from delete_tiffin.php)
if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $success_msg = "Tiffin deleted successfully.";
}

// Fetch cook's tiffins
$tiffins_sql = "SELECT * FROM tiffins WHERE cook_id = $cook_id ORDER BY name";
$tiffins = $conn->query($tiffins_sql);

// Fetch cook's orders with more details
$orders_sql = "SELECT o.*, t.name, t.price, u.username as user_name, u.location as user_location 
               FROM orders o 
               JOIN tiffins t ON o.tiffin_id = t.tiffin_id 
               JOIN users u ON o.user_id = u.user_id 
               WHERE t.cook_id = $cook_id 
               ORDER BY o.order_date DESC";
$orders = $conn->query($orders_sql);

if (!$tiffins || !$orders) {
    die("Database query error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Cook Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! <a href="../logout.php">Logout</a></p>
    </header>
    <nav>
        <a href="add_tiffin.php" class="btn">Add New Tiffin</a>
        <a href="../user/dashboard.php" class="btn">View as User</a>
        <a href="../index.php" class="btn">Home</a>
    </nav>
    <main>
        <?php if ($success_msg): ?><p class="success"><?php echo $success_msg; ?></p><?php endif; ?>
        <?php if ($error_msg): ?><p class="error"><?php echo $error_msg; ?></p><?php endif; ?>

        <section>
            <h2>Your Tiffins (<?php echo $tiffins->num_rows; ?> total)</h2>
            <?php if ($tiffins->num_rows > 0): ?>
                <div class="tiffin-grid">
                    <?php 
                    $tiffins->data_seek(0); // Reset pointer after num_rows
                    while ($tiffin = $tiffins->fetch_assoc()): 
                    ?>
                        <div class="tiffin-card">
                            <?php if ($tiffin['image']): ?>
                                <img src="../assets/images/<?php echo htmlspecialchars($tiffin['image']); ?>" alt="<?php echo htmlspecialchars($tiffin['name']); ?>" style="max-width: 200px;">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($tiffin['name']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($tiffin['description'], 0, 100)); ?>...</p>
                            <p><strong>Price:</strong> $<?php echo $tiffin['price']; ?></p>
                            <div class="actions">
                                <a href="edit_tiffin.php?id=<?php echo $tiffin['tiffin_id']; ?>" class="btn">Edit</a>
                                <a href="delete_tiffin.php?id=<?php echo $tiffin['tiffin_id']; ?>" 
                                   class="btn" 
                                   onclick="return confirm('Are you sure you want to delete this tiffin? Orders will remain in history.');">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No tiffins added yet. <a href="add_tiffin.php">Add one now!</a></p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Your Orders (<?php echo $orders->num_rows; ?> total)</h2>
            <?php if ($orders->num_rows > 0): ?>
                <ul class="order-list">
                    <?php 
                    $orders->data_seek(0); // Reset pointer
                    while ($order = $orders->fetch_assoc()): 
                        $total = $order['price'] * $order['quantity'];
                        $is_pending = ($order['status'] == 'pending');
                    ?>
                        <li class="order-item <?php echo $is_pending ? 'pending' : 'completed'; ?>">
                            <h3><?php echo htmlspecialchars($order['name']); ?> x<?php echo $order['quantity']; ?></h3>
                            <p><strong>Total:</strong> $<?php echo number_format($total, 2); ?></p>
                            <p><strong>From User:</strong> <?php echo htmlspecialchars($order['user_name']); ?> (<?php echo htmlspecialchars($order['user_location']); ?>)</p>
                            <p><strong>Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                            <p><strong>Status:</strong> <span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                            <?php if ($is_pending): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <input type="hidden" name="accept_order" value="1">
                                    <button type="submit" class="btn" style="background: #2196F3; color: white;">Accept Order</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No orders yet. Promote your tiffins to get some!</p>
            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/js/script.js"></script>
</body>
</html>