<?php
include '../db.php'; 
if (!isLoggedIn('cook')) { redirect('../login.php'); }

$cook_id = $_SESSION['user_id'];
$tiffin_id = intval($_GET['id'] ?? 0);

// Use prepared statement for initial fetch
$sql_fetch = "SELECT * FROM tiffins WHERE tiffin_id = ? AND cook_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("ii", $tiffin_id, $cook_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();

if ($result->num_rows == 0) { redirect('dashboard.php'); } // Not found or not owner's
$tiffin = $result->fetch_assoc();
$stmt_fetch->close();

$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Get and sanitize/validate inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = round(floatval($_POST['price']), 2);
    
    $image = $tiffin['image']; // Keep old if no new
    
    // 2. Handle image upload (same improved logic as add_tiffin.php)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
        finfo_close($file_info);

        if (in_array($mime_type, $allowed_types) && $_FILES['image']['size'] < 5000000) { // 5MB limit
            $new_image = time() . "_" . basename($_FILES['image']['name']); // Unique filename
            $target_dir = "../assets/images/";
            $target_file = $target_dir . $new_image;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Optional: Delete old image if it exists and isn't the default
                if (!empty($tiffin['image']) && file_exists($target_dir . $tiffin['image'])) {
                    // unlink($target_dir . $tiffin['image']); // Uncomment to delete old image
                }
                $image = $new_image;
            } else {
                $error = "Sorry, there was an error uploading the new file.";
            }
        } else {
            $error = "Invalid file type or file too large (max 5MB, accepted: JPG, PNG, GIF).";
        }
    }
    
    if (empty($error)) {
        // 3. Use Prepared Statement for Update
        $update_sql = "UPDATE tiffins SET name=?, description=?, price=?, image=? WHERE tiffin_id=? AND cook_id=?";
        $stmt_update = $conn->prepare($update_sql);
        // 'sdsi' for the values, 'i' for tiffin_id, 'i' for cook_id
        $stmt_update->bind_param("ssdsii", $name, $description, $price, $image, $tiffin_id, $cook_id);
        
        if ($stmt_update->execute()) {
            // $success = "Tiffin updated!"; // Not needed as we redirect
            redirect('dashboard.php?edited=1'); // Redirect after success with a success flag
        } else {
            $error = "Update failed: " . $conn->error;
        }
        $stmt_update->close();
    }
}

// Re-fetch tiffin details if POST failed to display current values in the form
if (!empty($error) && empty($success)) {
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("ii", $tiffin_id, $cook_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $tiffin = $result->fetch_assoc();
    $stmt_fetch->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tiffin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Tiffin</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($tiffin['name']); ?>" required></label>
            <label>Description: <textarea name="description" required><?php echo htmlspecialchars($tiffin['description']); ?></textarea></label>
            <label>Price: <input type="number" step="0.01" name="price" value="<?php echo $tiffin['price']; ?>" required></label>
            <label>Image (current: <?php echo htmlspecialchars($tiffin['image']); ?>): <input type="file" name="image" accept="image/*"></label>
            <button type="submit" class="btn">Update Tiffin</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>