<?php
session_start();
require_once '../config.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';
$edit_data = null;

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $result = mysqli_query($conn, "SELECT logo FROM jurusan WHERE id=$id");
    $row = mysqli_fetch_assoc($result);

    if ($row && $row['logo'] && file_exists("../assets/uploads/" . $row['logo'])) {
        unlink("../assets/uploads/" . $row['logo']);
    }

    mysqli_query($conn, "DELETE FROM jurusan WHERE id=$id");
    header("Location: jurusan.php?success=deleted");
    exit();
}

// EDIT
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM jurusan WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// SUBMIT
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_jurusan']);
    $singkatan = mysqli_real_escape_string($conn, $_POST['singkatan']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);
    $logo = null;

    if (!empty($_FILES['logo']['name'])) {
        $filename = time() . "_" . basename($_FILES["logo"]["name"]);
        $target = "../assets/uploads/" . $filename;

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target)) {
            $logo = $filename;
        }
    }

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);

        if ($logo) {
            mysqli_query($conn, 
                "UPDATE jurusan SET 
                nama_jurusan='$nama',
                singkatan='$singkatan',
                deskripsi='$deskripsi',
                warna='$warna',
                logo='$logo'
                WHERE id=$id");
        } else {
            mysqli_query($conn, 
                "UPDATE jurusan SET 
                nama_jurusan='$nama',
                singkatan='$singkatan',
                deskripsi='$deskripsi',
                warna='$warna'
                WHERE id=$id");
        }

        header("Location: jurusan.php?success=updated");
        exit();
    } else {
        mysqli_query($conn, 
            "INSERT INTO jurusan (nama_jurusan, singkatan, deskripsi, warna, logo) 
            VALUES ('$nama','$singkatan','$deskripsi','$warna','$logo')");
        header("Location: jurusan.php?success=added");
        exit();
    }
}

$jurusan = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Jurusan</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: #eef1f7;
        display: flex;
    }

    /* SIDEBAR */
    .sidebar {
        width: 250px;
        background: #1a1a2e;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        padding: 25px 0;
        color: white;
    }
    .sidebar h2 {
        text-align: center;
        margin-bottom: 25px;
    }
    .sidebar a {
        display: block;
        padding: 12px 25px;
        color: #dbe2ef;
        text-decoration: none;
        font-size: 14px;
        margin: 3px 0;
        transition: 0.2s;
    }
    .sidebar a:hover,
    .sidebar .active {
        background: #667eea;
        color: white;
    }

    /* CONTENT */
    .content {
        margin-left: 250px;
        padding: 30px;
        width: calc(100% - 250px);
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
        margin-top: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full {
        grid-column: span 2;
    }

    label {
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
    }

    input[type="text"],
    input[type="file"],
    textarea {
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #ccc;
        font-size: 15px;
    }

    textarea {
        resize: vertical;
    }

    .btn {
        background: #667eea;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: 15px;
        width: 160px;
        margin-top: 10px;
        transition: .2s;
    }

    .btn:hover {
        background: #5a6fd8;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 18px;
    }
    th {
        background: #667eea;
        color: white;
        padding: 12px;
        font-size: 14px;
    }
    td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .aksi a {
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        text-decoration: none;
        margin-right: 4px;
        color: white;
    }
    .edit { background: #20c997; }
    .delete { background: #e74c3c; }
</style>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="index.php">🏠 Dashboard</a>
    <a class="active" href="jurusan.php">📚 Kelola Jurusan</a>
    <a href="guru.php">👨‍🏫 Kelola Guru</a>
    <a href="ekskul.php">⚽ Kelola Ekskul</a>
    <a href="event.php">📅 Kelola Event</a>
    <a href="contact.php">📞 Pesan Masuk</a>
    <a href="logout.php" style="color:#ff6b6b;">🚪 Logout</a>
</div>

<!-- MAIN CONTENT -->
<div class="content">

    <div class="card">
        <h2>🔧 Tambah / Edit Jurusan</h2>

        <form method="POST" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">

            <div class="form-group">
                <label>Nama Jurusan</label>
                <input type="text" name="nama_jurusan" required value="<?= $edit_data['nama_jurusan'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Singkatan</label>
                <input type="text" name="singkatan" required value="<?= $edit_data['singkatan'] ?? '' ?>">
            </div>

            <div class="form-group full">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Warna Header</label>
                <input type="text" name="warna" placeholder="Contoh: #667eea" value="<?= $edit_data['warna'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Logo Jurusan</label>
                <input type="file" name="logo">
                <?php if (!empty($edit_data['logo'])): ?>
                    <small><img src="../assets/uploads/<?= $edit_data['logo'] ?>" width="70" style="margin-top:8px;border-radius:6px;"></small>
                <?php endif; ?>
            </div>

            <div class="form-group full">
                <button type="submit" name="submit" class="btn">Simpan</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>📋 Daftar Jurusan</h2>

        <table>
            <tr>
                <th>Logo</th>
                <th>Nama Jurusan</th>
                <th>Singkatan</th>
                <th>Aksi</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($jurusan)): ?>
            <tr>
                <td>
                    <?php if ($row['logo']): ?>
                        <img src="../assets/uploads/<?= $row['logo'] ?>" width="50">
                    <?php else: ?> ❌ <?php endif; ?>
                </td>
                <td><?= $row['nama_jurusan'] ?></td>
                <td><?= $row['singkatan'] ?></td>
                <td class="aksi">
                    <a class="edit" href="jurusan.php?edit=<?= $row['id'] ?>">Edit</a>
                    <a class="delete" onclick="return confirm('Hapus jurusan ini?')" href="jurusan.php?delete=<?= $row['id'] ?>">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>

        </table>
    </div>

</div>

</body>
</html>
