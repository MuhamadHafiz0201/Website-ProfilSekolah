<?php
session_start();
require_once '../config.php';

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$edit_data = null;

// EDIT (asumsinya cuma ada satu baris contact)
$result = mysqli_query($conn, "SELECT * FROM contact LIMIT 1");
$edit_data = mysqli_fetch_assoc($result);

// SUBMIT
if (isset($_POST['submit'])) {
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $jam_operasional = mysqli_real_escape_string($conn, $_POST['jam_operasional']);
    $maps_embed = mysqli_real_escape_string($conn, $_POST['maps_embed']);

    if ($edit_data) {
        // UPDATE
        $id = $edit_data['id'];
        mysqli_query($conn, "UPDATE contact SET 
            alamat='$alamat',
            telepon='$telepon',
            email='$email',
            website='$website',
            jam_operasional='$jam_operasional',
            maps_embed='$maps_embed'
            WHERE id=$id");
    } else {
        // INSERT
        mysqli_query($conn, "INSERT INTO contact (alamat, telepon, email, website, jam_operasional, maps_embed) 
            VALUES ('$alamat','$telepon','$email','$website','$jam_operasional','$maps_embed')");
    }

    header("Location: contact.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Contact</title>
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

.form-grid { display:grid; grid-template-columns:1fr; gap:18px; margin-top:15px; }
.form-group { display:flex; flex-direction:column; }
label { font-weight:600; margin-bottom:6px; font-size:14px; }
input[type="text"], input[type="email"], textarea { padding:12px; border-radius:10px; border:1px solid #ccc; font-size:15px; }
textarea { resize:vertical; }

.btn { background:#667eea; color:white; padding:12px 20px; border-radius:10px; border:none; cursor:pointer; font-size:15px; width:160px; margin-top:10px; transition:.2s; }
.btn:hover { background:#5a6fd8; }
</style>
</head>
<body>

<div class="sidebar">
<h2>Admin Panel</h2>
<a href="index.php">🏠 Dashboard</a>
<a href="jurusan.php">📚 Kelola Jurusan</a>
<a href="guru.php">👨‍🏫 Kelola Guru</a>
<a href="ekskul.php">⚽ Kelola Ekskul</a>
<a href="event.php">📅 Kelola Event</a>
<a class="active" href="contact.php">📞 Kelola Contact</a>
<a href="logout.php" style="color:#ff6b6b;">🚪 Logout</a>
</div>

<div class="content">

<div class="card">
<h2>🔧 Edit Contact Sekolah</h2>

<form method="POST" class="form-grid">
<div class="form-group">
<label>Alamat</label>
<input type="text" name="alamat" value="<?= $edit_data['alamat'] ?? '' ?>" required>
</div>

<div class="form-group">
<label>Telepon</label>
<input type="text" name="telepon" value="<?= $edit_data['telepon'] ?? '' ?>" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?= $edit_data['email'] ?? '' ?>" required>
</div>

<div class="form-group">
<label>Website</label>
<input type="text" name="website" value="<?= $edit_data['website'] ?? '' ?>">
</div>

<div class="form-group">
<label>Jam Operasional</label>
<input type="text" name="jam_operasional" value="<?= $edit_data['jam_operasional'] ?? '' ?>">
</div>

<div class="form-group">
<label>Maps Embed</label>
<textarea name="maps_embed" rows="4"><?= $edit_data['maps_embed'] ?? '' ?></textarea>
</div>

<div class="form-group">
<button type="submit" name="submit" class="btn">Simpan</button>
</div>
</form>
</div>

</div>
</body>
</html>
