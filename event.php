<?php
session_start();
require_once '../config.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$edit_data = null;

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $result = mysqli_query($conn, "SELECT foto FROM event WHERE id=$id");
    $row = mysqli_fetch_assoc($result);

    if ($row && $row['foto'] && file_exists("../assets/uploads/" . $row['foto'])) {
        unlink("../assets/uploads/" . $row['foto']);
    }

    mysqli_query($conn, "DELETE FROM event WHERE id=$id");
    header("Location: event.php");
    exit();
}

// EDIT
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM event WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($result);
}

// SUBMIT
if (isset($_POST['submit'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $foto = null;

    if (!empty($_FILES['foto']['name'])) {
        $filename = time() . "_" . basename($_FILES["foto"]["name"]);
        $target = "../assets/uploads/" . $filename;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target)) {
            $foto = $filename;
        }
    }

    if (!empty($_POST['id'])) {
        $id = intval($_POST['id']);
        if ($foto) {
            mysqli_query($conn, "UPDATE event SET 
                judul='$judul',
                deskripsi='$deskripsi',
                tanggal='$tanggal',
                lokasi='$lokasi',
                foto='$foto'
                WHERE id=$id");
        } else {
            mysqli_query($conn, "UPDATE event SET 
                judul='$judul',
                deskripsi='$deskripsi',
                tanggal='$tanggal',
                lokasi='$lokasi'
                WHERE id=$id");
        }
        header("Location: event.php");
        exit();
    } else {
        mysqli_query($conn, "INSERT INTO event (judul, deskripsi, tanggal, lokasi, foto) 
            VALUES ('$judul','$deskripsi','$tanggal','$lokasi','$foto')");
        header("Location: event.php");
        exit();
    }
}

$events = mysqli_query($conn, "SELECT * FROM event ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Event</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body { margin:0; font-family:'Poppins',sans-serif; background:#eef1f7; display:flex; }

/* SIDEBAR */
.sidebar { width:250px; background:#1a1a2e; height:100vh; position:fixed; left:0; top:0; padding:25px 0; color:white; }
.sidebar h2 { text-align:center; margin-bottom:25px; }
.sidebar a { display:block; padding:12px 25px; color:#dbe2ef; text-decoration:none; font-size:14px; margin:3px 0; transition:0.2s; }
.sidebar a:hover, .sidebar .active { background:#667eea; color:white; }

/* CONTENT */
.content { margin-left:250px; padding:30px; width:calc(100% - 250px); }
.card { background:white; padding:25px; border-radius:14px; box-shadow:0 4px 18px rgba(0,0,0,0.08); margin-bottom:30px; }

.form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:18px; margin-top:15px; }
.form-group { display:flex; flex-direction:column; }
.form-group.full { grid-column:span 2; }
label { font-weight:600; margin-bottom:6px; font-size:14px; }
input[type="text"], input[type="date"], input[type="file"], textarea { padding:12px; border-radius:10px; border:1px solid #ccc; font-size:15px; }
textarea { resize:vertical; }

.btn { background:#667eea; color:white; padding:12px 20px; border-radius:10px; border:none; cursor:pointer; font-size:15px; width:160px; margin-top:10px; transition:.2s; }
.btn:hover { background:#5a6fd8; }

table { width:100%; border-collapse:collapse; margin-top:18px; }
th { background:#667eea; color:white; padding:12px; font-size:14px; }
td { padding:12px; border-bottom:1px solid #eee; text-align:center; }
.aksi a { padding:7px 12px; border-radius:6px; font-size:12px; text-decoration:none; margin-right:4px; color:white; }
.edit { background:#20c997; }
.delete { background:#e74c3c; }
</style>
</head>
<body>

<div class="sidebar">
<h2>Admin Panel</h2>
<a href="index.php">🏠 Dashboard</a>
<a href="jurusan.php">📚 Kelola Jurusan</a>
<a href="guru.php">👨‍🏫 Kelola Guru</a>
<a href="ekskul.php">⚽ Kelola Ekskul</a>
<a class="active" href="event.php">📅 Kelola Event</a>
<a href="contact.php">📞 Pesan Masuk</a>
<a href="logout.php" style="color:#ff6b6b;">🚪 Logout</a>
</div>

<div class="content">

<div class="card">
<h2>🔧 Tambah / Edit Event</h2>

<form method="POST" enctype="multipart/form-data" class="form-grid">
<input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">

<div class="form-group">
<label>Judul Event</label>
<input type="text" name="judul" required value="<?= $edit_data['judul'] ?? '' ?>">
</div>

<div class="form-group full">
<label>Deskripsi</label>
<textarea name="deskripsi" rows="4"><?= $edit_data['deskripsi'] ?? '' ?></textarea>
</div>

<div class="form-group">
<label>Tanggal</label>
<input type="date" name="tanggal" value="<?= $edit_data['tanggal'] ?? '' ?>">
</div>

<div class="form-group">
<label>Lokasi</label>
<input type="text" name="lokasi" value="<?= $edit_data['lokasi'] ?? '' ?>">
</div>

<div class="form-group">
<label>Foto</label>
<input type="file" name="foto">
<?php if (!empty($edit_data['foto'])): ?>
<small><img src="../assets/uploads/<?= $edit_data['foto'] ?>" width="70" style="margin-top:8px;border-radius:6px;"></small>
<?php endif; ?>
</div>

<div class="form-group full">
<button type="submit" name="submit" class="btn">Simpan</button>
</div>
</form>
</div>

<div class="card">
<h2>📋 Daftar Event</h2>
<table>
<tr>
<th>Foto</th>
<th>Judul</th>
<th>Deskripsi</th>
<th>Tanggal</th>
<th>Lokasi</th>
<th>Aksi</th>
</tr>
<?php while ($row = mysqli_fetch_assoc($events)): ?>
<tr>
<td>
<?php if ($row['foto']): ?>
<img src="../assets/uploads/<?= $row['foto'] ?>" width="50">
<?php else: ?> ❌ <?php endif; ?>
</td>
<td><?= $row['judul'] ?></td>
<td><?= $row['deskripsi'] ?></td>
<td><?= $row['tanggal'] ?></td>
<td><?= $row['lokasi'] ?></td>
<td class="aksi">
<a class="edit" href="event.php?edit=<?= $row['id'] ?>">Edit</a>
<a class="delete" onclick="return confirm('Hapus event ini?')" href="event.php?delete=<?= $row['id'] ?>">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>
