<?php
$conn = mysqli_connect("localhost", "root", "", "db_sekolah");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


mysqli_set_charset($conn, "utf8mb4");

// Fungsi helper untuk clean input
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

// Redirect if not logged in
function require_admin() {
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi untuk upload gambar
function upload_image($file, $folder = 'uploads/') {
    $target_dir = "../assets/img/" . $folder;
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    if ($file["size"] > 5000000) {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $folder . $new_filename;
    }
    
    return false;
}

// Base URL
$base_url = 'http://localhost/smk_cibitung1/';
?>