<?php
session_start(); // Start session for all pages
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tiffin_portal";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Function to check if user is logged in and get role
function isLoggedIn($role = null) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    if ($role && $_SESSION['role'] !== $role) {
        return false;
    }
    return true;
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>