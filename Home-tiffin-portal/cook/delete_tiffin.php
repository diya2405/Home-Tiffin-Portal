<?php 
include '../db.php'; 
if (!isLoggedIn('cook')) { redirect('../login.php'); }

$cook_id = $_SESSION['user_id'];
$tiffin_id = intval($_GET['id'] ?? 0);

if ($tiffin_id > 0) {
    // Verify ownership
    $check_sql = "SELECT tiffin_id FROM tiffins WHERE tiffin_id = $tiffin_id AND cook_id = $cook_id";
    if ($conn->query($check_sql)->num_rows > 0) {
        // Delete the tiffin (orders remain for history)
        $delete_sql = "DELETE FROM tiffins WHERE tiffin_id = $tiffin_id";
        $conn->query($delete_sql);
    }
}

redirect('dashboard.php?deleted=1');
?>