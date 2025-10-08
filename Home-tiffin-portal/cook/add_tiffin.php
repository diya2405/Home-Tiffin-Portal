<?php 
include '../db.php'; 
if (!isLoggedIn('cook')) { redirect('../login.php'); }

$cook_id = $_SESSION['user_id'];
$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Get and sanitize/validate inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = round(floatval($_POST['price']), 2); // Ensure price is a valid decimal
    
    if (empty($name) || empty($description) || $price <= 0) {
        $error = "Please fill all fields correctly.";
    } else {
        $image = ''; // Default
        
        // 2. Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($file_info, $_FILES['image']['tmp_name']);
            finfo_close($file_info);

            if (in_array($mime_type, $allowed_types) && $_FILES['image']['size'] < 5000000) { // 5MB limit
                $image = time() . "_" . basename($_FILES['image']['name']); // Unique filename to prevent overwrite
                $target_dir = "../assets/images/";
                $target_file = $target_dir . $image;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error = "Sorry, there was an error uploading your file.";
                    $image = ''; // Clear image if upload fails
                }
            } else {
                $error = "Invalid file type or file too large (max 5MB, accepted: JPG, PNG, GIF).";
            }
        }
        
        if (empty($error)) {
            // 3. Use Prepared Statement for Insertion
            $sql = "INSERT INTO tiffins (cook_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            // 'isds' stands for: integer, string, string, double, string
            $stmt->bind_param("issds", $cook_id, $name, $description, $price, $image);
            
            if ($stmt->execute()) {
                // $success = "Tiffin added successfully!"; // Not needed as we redirect
                redirect('dashboard.php?added=1'); // Redirect after success with a success flag
            } else {
                $error = "Failed to add tiffin: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tiffin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
     <div class="form-container">
        <h2>Add New Tiffin</h2>
        <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?php echo $success; ?></p><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required></label>
            <label>Description: <textarea name="description" required><?php echo htmlspecialchars($description ?? ''); ?></textarea></label>
            <label>Price: <input type="number" step="0.01" name="price" value="<?php echo $price ?? ''; ?>" required></label>
            <label>Image: <input type="file" name="image" accept="image/*"></label>
            <button type="submit" class="btn">Add Tiffin</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>