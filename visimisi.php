<?php
require_once '../config.php';
require_admin();

// Hapus Data
if(isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    mysqli_query($conn, "DELETE FROM visimisi WHERE id = '$id'");
    header("Location: visimisi.php?success=delete");
    exit();
}

// Tambah/Edit Data
if(isset($_POST['submit'])) {
    $tipe = clean_input($_POST['tipe']);
    $konten = clean_input($_POST['konten']);
    $urutan = clean_input($_POST['urutan']);
    
    if(!empty($_POST['id'])) {
        $id = clean_input($_POST['id']);
        $query = "UPDATE visimisi 
                  SET tipe='$tipe', konten='$konten', urutan='$urutan' 
                  WHERE id='$id'";
    } else {
        $query = "INSERT INTO visimisi (tipe, konten, urutan) 
                  VALUES ('$tipe', '$konten', '$urutan')";
    }

    mysqli_query($conn, $query);
    header("Location: visimisi.php?success=save");
    exit();
}

// Data edit jika ada
$edit_data = null;
if(isset($_GET['edit'])) {
    $id = clean_input($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM visimisi WHERE id='$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// Ambil data
$visi = mysqli_query($conn, "SELECT * FROM visimisi WHERE tipe='visi' ORDER BY urutan ASC");
$misi = mysqli_query($conn, "SELECT * FROM visimisi WHERE tipe='misi' ORDER BY urutan ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Visi & Misi | Admin SMK Cibitung 1</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; }

        .admin-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 260px; background: #4C57D6; color: white;
            position: fixed; height: 100vh; padding-top: 20px;
        }
        .sidebar h2 { text-align: center; font-size: 19px; margin-bottom: 18px; }
        .sidebar-menu { list-style: none; margin-top: 20px; }
        .sidebar-menu a {
            display: block; padding: 13px 25px;
            font-size: 15px; color: #fff; text-decoration: none;
            transition: .3s;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #2F3EB4;
        }

        .main-content {
            margin-left: 260px;
            flex: 1; padding: 30px;
        }
        .top-bar { background: white; padding: 15px 25px;
            border-radius: 12px; margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .content-box {
            background: white; padding: 25px;
            border-radius: 12px; margin-bottom: 25px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .content-box h2 { margin-bottom: 15px; }

        table { width: 100%; border-collapse: collapse; }
        table th, table td {
            padding: 12px; border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .btn {
            padding: 10px 18px; border-radius: 8px;
            text-decoration: none; font-size: 13px;
        }
        .btn-primary { background: #4C57D6; color: #fff; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
    </style>
</head>
<body>

<div class="admin-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <div class="sidebar-menu">
            <a href="index.php">📊 Dashboard</a>
            <a href="visimisi.php" class="active">🎯 Kelola Visi & Misi</a>
            <a href="jurusan.php">🎓 Kelola Jurusan</a>
            <a href="ekskul.php">⚽ Kelola Ekskul</a>
            <a href="guru.php">👨‍🏫 Kelola Guru</a>
            <a href="event.php">🎉 Kelola Event</a>
            <a href="contact.php">📞 Kelola Contact</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <div class="top-bar">
            <h1>🎯 Kelola Visi & Misi</h1>
        </div>

        <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success" style="background:#d4edda;padding:12px 15px;margin-bottom:20px;border-radius:8px;">
            Data berhasil <?= $_GET['success']=='delete' ? 'dihapus' : 'disimpan' ?>!
        </div>
        <?php endif; ?>

        <!-- Form Input -->
        <div class="content-box">
            <h2><?= $edit_data ? 'Edit' : 'Tambah' ?> Visi / Misi</h2>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">

                <label>Tipe</label>
                <select name="tipe" required style="width:100%;padding:10px;margin-bottom:15px;border-radius:8px;border:1px solid #ccc;">
                    <option value="visi" <?= ($edit_data['tipe'] ?? '')=='visi'?'selected':'' ?>>Visi</option>
                    <option value="misi" <?= ($edit_data['tipe'] ?? '')=='misi'?'selected':'' ?>>Misi</option>
                </select>

                <label>Konten</label>
                <textarea name="konten" required style="width:100%;padding:12px;height:100px;border-radius:8px;border:1px solid #ccc;margin-bottom:15px;"><?= $edit_data['konten'] ?? '' ?></textarea>

                <label>Urutan</label>
                <input type="number" name="urutan" required
                       value="<?= $edit_data['urutan'] ?? '1' ?>"
                       style="width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-bottom:20px;">

                <button type="submit" name="submit" class="btn btn-primary">
                    <?= $edit_data ? 'Update' : 'Simpan' ?>
                </button>
                <?php if($edit_data): ?>
                    <a href="visimisi.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Data Visi -->
        <div class="content-box">
            <h2>📋 Data Visi</h2>
            <table>
                <thead>
                    <tr><th>No</th><th>Konten</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $no=1; while($v = mysqli_fetch_assoc($visi)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $v['konten'] ?></td>
                        <td>
                            <a class="btn-edit" href="?edit=<?= $v['id'] ?>">Edit</a>
                            <a class="btn-delete" href="?delete=<?= $v['id'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile;?>
                </tbody>
            </table>
        </div>

        <!-- Data Misi -->
        <div class="content-box">
            <h2>📋 Data Misi</h2>
            <table>
                <thead>
                    <tr><th>No</th><th>Konten</th><th>Urutan</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $no=1; while($m = mysqli_fetch_assoc($misi)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $m['konten'] ?></td>
                        <td><?= $m['urutan'] ?></td>
                        <td>
                            <a class="btn-edit" href="?edit=<?= $m['id'] ?>">Edit</a>
                            <a class="btn-delete" href="?delete=<?= $m['id'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile;?>
                </tbody>
            </table>
        </div>

    </main>

</div>

</body>
</html>
