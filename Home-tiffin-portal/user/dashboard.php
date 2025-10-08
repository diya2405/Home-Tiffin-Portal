<?php 
include '../db.php'; 
if (!isLoggedIn('user')) { redirect('../login.php'); }

$user_id = $_SESSION['user_id'];

// Fetch all tiffins (from all cooks)
$tiffins_sql = "SELECT t.*, u.location, u.username FROM tiffins t JOIN users u ON t.cook_id = u.user_id ORDER BY t.name";
$tiffins = $conn->query($tiffins_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>User Dashboard</h1>
        <p><a href="../logout.php">Logout</a></p>
    </header>
    <nav>
        <a href="order_history.php" class="btn">Order History</a>
    </nav>
    <main>
        <section>
            <h2>Available Tiffins</h2>
            <?php if ($tiffins->num_rows > 0): ?>
                <div class="tiffin-grid">
                    <?php while ($tiffin = $tiffins->fetch_assoc()): ?>
                        <div class="tiffin-card">
                            <?php if ($tiffin['image']): ?>
                                <img src="../assets/images/<?php echo htmlspecialchars($tiffin['image']); ?>" alt="<?php echo htmlspecialchars($tiffin['name']); ?>" style="max-width: 200px;">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($tiffin['name']); ?></h3>
                            <p><?php echo htmlspecialchars($tiffin['description']); ?></p>
                            <p>Price: $<?php echo $tiffin['price']; ?></p>
                            <p>By: <?php echo htmlspecialchars($tiffin['username']); ?> (<?php echo htmlspecialchars($tiffin['location']); ?>)</p>
                            <a href="order_tiffin.php?id=<?php echo $tiffin['tiffin_id']; ?>" class="btn">Order Now</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No tiffins available yet. Check back later!</p>
            <?php endif; ?>
        </section>
    </main>
    <script src="../assets/js/script.js"></script>
</body>
</html>