<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: index.php');
    exit;
}

$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_mobil']));
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $kondisi = mysqli_real_escape_string($conn, trim($_POST['kondisi']));
    $harga = (int) ($_POST['harga'] ?? 0);

    if($nama === '' || $jumlah <= 0 || $kondisi === '' || $harga <= 0){
        $message = 'Lengkapi data mobil dengan benar.';
    } else {
        mysqli_query($conn, "INSERT INTO mobil (nama_mobil, jumlah, kondisi, harga_sewa) VALUES ('$nama', '$jumlah', '$kondisi', '$harga')");
        header('Location: dashboard_admin.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-5">
    <div class="glass card-modern p-4">
        <h3 class="mb-4">Tambah Mobil</h3>
        <?php if($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Mobil</label>
                <input type="text" name="nama_mobil" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Kondisi</label>
                <input type="text" name="kondisi" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga Sewa</label>
                <input type="number" name="harga" class="form-control" min="1" required>
            </div>
            <div class="d-flex gap-2">
                <button class="btn-modern">Simpan</button>
                <a href="dashboard_admin.php" class="btn-modern btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
