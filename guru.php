<?php
session_start();
require_once '../config.php';
if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

$success='';
$error='';
$edit_data=null;

// DELETE
if(isset($_GET['delete'])){
    $id=intval($_GET['delete']);
    $result=mysqli_query($conn,"SELECT foto FROM guru WHERE id=$id");
    $row=mysqli_fetch_assoc($result);
    if($row && $row['foto'] && file_exists("../assets/uploads/".$row['foto'])){
        unlink("../assets/uploads/".$row['foto']);
    }
    mysqli_query($conn,"DELETE FROM guru WHERE id=$id");
    header("Location:guru.php?success=deleted");
    exit();
}

// EDIT
if(isset($_GET['edit'])){
    $id=intval($_GET['edit']);
    $result=mysqli_query($conn,"SELECT * FROM guru WHERE id=$id");
    $edit_data=mysqli_fetch_assoc($result);
}

// SUBMIT
if(isset($_POST['submit'])){
    $nama=mysqli_real_escape_string($conn,$_POST['nama']);
    $jabatan=mysqli_real_escape_string($conn,$_POST['jabatan']);
    $mapel=mysqli_real_escape_string($conn,$_POST['mata_pelajaran']);
    $email=mysqli_real_escape_string($conn,$_POST['email']);
    $telepon=mysqli_real_escape_string($conn,$_POST['telepon']);
    $foto=null;

    if(!empty($_FILES['foto']['name'])){
        $filename=time().'_'.basename($_FILES['foto']['name']);
        $target="../assets/uploads/".$filename;
        if(move_uploaded_file($_FILES['foto']['tmp_name'],$target)){
            $foto=$filename;
        }
    }

    if(!empty($_POST['id'])){
        $id=intval($_POST['id']);
        if($foto){
            mysqli_query($conn,"UPDATE guru SET nama='$nama', jabatan='$jabatan', mata_pelajaran='$mapel', email='$email', telepon='$telepon', foto='$foto' WHERE id=$id");
        }else{
            mysqli_query($conn,"UPDATE guru SET nama='$nama', jabatan='$jabatan', mata_pelajaran='$mapel', email='$email', telepon='$telepon' WHERE id=$id");
        }
        header("Location:guru.php?success=updated");
        exit();
    }else{
        mysqli_query($conn,"INSERT INTO guru (nama,jabatan,mata_pelajaran,email,telepon,foto) VALUES ('$nama','$jabatan','$mapel','$email','$telepon','$foto')");
        header("Location:guru.php?success=added");
        exit();
    }
}

$guru=mysqli_query($conn,"SELECT * FROM guru ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Guru</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body{margin:0;font-family:'Poppins',sans-serif;background:#eef1f7;display:flex;}
.sidebar{width:250px;background:#1a1a2e;height:100vh;position:fixed;left:0;top:0;padding:25px 0;color:white;}
.sidebar h2{text-align:center;margin-bottom:25px;}
.sidebar a{display:block;padding:12px 25px;color:#dbe2ef;text-decoration:none;font-size:14px;margin:3px 0;transition:0.2s;}
.sidebar a:hover,.sidebar .active{background:#667eea;color:white;}
.content{margin-left:250px;padding:30px;width:calc(100%-250px);}
.card{background:white;padding:25px;border-radius:14px;box-shadow:0 4px 18px rgba(0,0,0,0.08);margin-bottom:30px;}
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px;margin-top:15px;}
.form-group{display:flex;flex-direction:column;}
.form-group.full{grid-column:span 2;}
label{font-weight:600;margin-bottom:6px;font-size:14px;}
input[type="text"],input[type="file"],textarea{padding:12px;border-radius:10px;border:1px solid #ccc;font-size:15px;}
textarea{resize:vertical;}
.btn{background:#667eea;color:white;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;font-size:15px;width:160px;margin-top:10px;transition:.2s;}
.btn:hover{background:#5a6fd8;}
table{width:100%;border-collapse:collapse;margin-top:18px;}
th{background:#667eea;color:white;padding:12px;font-size:14px;}
td{padding:12px;border-bottom:1px solid #eee;}
.aksi a{padding:7px 12px;border-radius:6px;font-size:12px;text-decoration:none;margin-right:4px;color:white;}
.edit{background:#20c997;}
.delete{background:#e74c3c;}
</style>
</head>
<body>
<div class="sidebar">
<h2>Admin Panel</h2>
<a href="index.php">🏠 Dashboard</a>
<a href="jurusan.php">📚 Kelola Jurusan</a>
<a class="active" href="guru.php">👨‍🏫 Kelola Guru</a>
<a href="ekskul.php">⚽ Kelola Ekskul</a>
<a href="event.php">📅 Kelola Event</a>
<a href="contact.php">📞 Pesan Masuk</a>
<a href="logout.php" style="color:#ff6b6b;">🚪 Logout</a>
</div>

<div class="content">
<div class="card">
<h2>🔧 Tambah / Edit Guru</h2>
<form method="POST" enctype="multipart/form-data" class="form-grid">
<input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
<div class="form-group"><label>Nama Guru</label><input type="text" name="nama" required value="<?= $edit_data['nama'] ?? '' ?>"></div>
<div class="form-group"><label>Jabatan</label><input type="text" name="jabatan" required value="<?= $edit_data['jabatan'] ?? '' ?>"></div>
<div class="form-group"><label>Mata Pelajaran</label><input type="text" name="mata_pelajaran" required value="<?= $edit_data['mata_pelajaran'] ?? '' ?>"></div>
<div class="form-group"><label>Email</label><input type="text" name="email" value="<?= $edit_data['email'] ?? '' ?>"></div>
<div class="form-group"><label>Telepon</label><input type="text" name="telepon" value="<?= $edit_data['telepon'] ?? '' ?>"></div>
<div class="form-group"><label>Foto Guru</label><input type="file" name="foto">
<?php if(!empty($edit_data['foto'])): ?>
<small><img src="../assets/uploads/<?= $edit_data['foto'] ?>" width="70" style="margin-top:8px;border-radius:6px;"></small>
<?php endif; ?>
</div>
<div class="form-group full"><button type="submit" name="submit" class="btn">Simpan</button></div>
</form>
</div>

<div class="card">
<h2>📋 Daftar Guru</h2>
<table>
<tr><th>Foto</th><th>Nama</th><th>Jabatan</th><th>Mata Pelajaran</th><th>Aksi</th></tr>
<?php while($row=mysqli_fetch_assoc($guru)): ?>
<tr>
<td><?php if($row['foto']): ?><img src="../assets/uploads/<?= $row['foto'] ?>" width="50"><?php else: ?>❌<?php endif; ?></td>
<td><?= $row['nama'] ?></td>
<td><?= $row['jabatan'] ?></td>
<td><?= $row['mata_pelajaran'] ?></td>
<td class="aksi">
<a class="edit" href="guru.php?edit=<?= $row['id'] ?>">Edit</a>
<a class="delete" onclick="return confirm('Hapus guru ini?')" href="guru.php?delete=<?= $row['id'] ?>">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>
</div>
</body>
</html>
