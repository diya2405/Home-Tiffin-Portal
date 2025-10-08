<?php 
include '../db.php'; 
if (!isLoggedIn('user')) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];
$tiffin_id = intval($_GET['id'] ?? 0);

if ($tiffin_id <= 0) { redirect('dashboard.php'); }

// Fetch tiffin details
$tiffin_sql = "SELECT * FROM tiffins WHERE tiffin_id = $tiffin_id";
$tiffin_result = $conn->query($tiffin_sql);
if ($tiffin_result->num_rows == 0) { redirect('dashboard.php'); }
$tiffin = $tiffin_result->fetch_assoc();

$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0) {
        $sql = "INSERT INTO orders (tiffin_id, user_id, quantity) VALUES ($tiffin_id, $user_id, $quantity)";
        if ($conn->query($sql)) {
            $success = "Order placed successfully! Check your order history.";
            redirect('order_history.php'); // Redirect after success
        } else {
            $error = "Order failed: " . $conn->error;
        }
    } else {
        $error = "Quantity must be at least 1.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tiffin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Order: <?php echo htmlspecialchars($tiffin['name']); ?></h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
        
        <div class="tiffin-details">
            <?php if ($tiffin['image']): ?>
                <img src="../assets/images/<?php echo htmlspecialchars($tiffin['image']); ?>" alt="<?php echo htmlspecialchars($tiffin['name']); ?>" style="max-width: 200px;">
            <?php endif; ?>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($tiffin['description']); ?></p>
            <p><strong>Price:</strong> $<?php echo $tiffin['price']; ?> per unit</p>
        </div>
        
        <form method="POST">
            <label>Quantity: <input type="number" name="quantity" min="1" value="1" required></label>
            <button type="submit" class="btn">Place Order</button>
        </form>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>