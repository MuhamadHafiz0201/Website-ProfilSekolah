<?php
session_start();
require_once '../config.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #f1f3f6;
        display: flex;
    }

    .sidebar {
        width: 250px;
        background: #1a1a2e;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding: 20px 0;
        color: white;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 22px;
    }

    .sidebar a {
        display: block;
        padding: 12px 25px;
        color: #dbe2ef;
        text-decoration: none;
        margin: 5px 0;
        transition: 0.3s;
        font-size: 14px;
    }

    .sidebar a:hover,
    .sidebar .active {
        background: #667eea;
        color: white;
    }

    .content {
        margin-left: 250px;
        padding: 30px;
        width: 100%;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 25px;
    }

    h1 {
        margin-bottom: 15px;
    }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="index.php" class="active">🏠 Dashboard</a>
    <a href="jurusan.php">📚 Kelola Jurusan</a>
    <a href="guru.php">👨‍🏫 Kelola Guru</a>
    <a href="ekskul.php">⚽ Kelola Ekskul</a>
    <a href="event.php">📅 Kelola Event</a>
    <a href="contact.php">📞 Pesan Masuk</a>
    <a href="logout.php" style="color:#ff6b6b;">🚪 Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    <h1>Selamat Datang, Admin 👋</h1>

    <div class="card">
        <h2>Ringkasan Sistem</h2>

        <p>Anda dapat mengelola semua data website SMK Cibitung 1 melalui panel ini:</p>
        <ul>
            <li>📚 Data Jurusan</li>
            <li>👨‍🏫 Data Guru</li>
            <li>⚽ Data Ekstrakurikuler</li>
            <li>📅 Data Event Sekolah</li>
            <li>📞 Pesan Kontak dari User</li>
        </ul>
    </div>
</div>

</body>
</html>
