<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="d-flex dashboard">
    <div class="sidebar glass">
        <h2>🚗 Rental</h2>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="data_mobil.php" class="active">Data Mobil</a>
        <a href="laporan_sewa.php">Laporan Sewa</a>
        <a href="laporan_keuangan.php">Laporan Keuangan</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="glass card-modern mb-4">
            <h3>Data Mobil</h3>
            <p>Kelola data kendaraan yang tersedia untuk disewa.</p>
            <a href="tambah_mobil.php" class="btn-modern">Tambah Mobil Baru</a>
        </div>

        <div class="glass card-modern">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Mobil</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Kondisi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $data = mysqli_query($conn, "SELECT *, status_mobil(jumlah) AS status FROM mobil");
                $no = 1;
                while($d = mysqli_fetch_assoc($data)){
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($d['nama_mobil']); ?></td>
                        <td><?= (int) $d['jumlah']; ?></td>
                        <td>
                            <?php if($d['status'] == 'Tersedia'): ?>
                                <span class="badge-available">Tersedia</span>
                            <?php else: ?>
                                <span class="badge-empty">Tidak tersedia</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($d['kondisi']); ?></td>
                        <td>Rp <?= number_format($d['harga_sewa']); ?></td>
                        <td>
                            <a href="edit_mobil.php?id=<?= (int) $d['id_mobil']; ?>" class="btn-modern">Edit</a>
                            <a href="hapus_mobil.php?id=<?= (int) $d['id_mobil']; ?>" class="btn-modern btn-danger" onclick="return confirm('Hapus mobil ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="glass card-modern mt-4">
            <h4 class="mb-4">Laporan Peminjaman</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Jumlah Sewa</th>
                        <th>Tanggal Sewa</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $laporan = mysqli_query($conn, "SELECT p.*, u.nama AS nama_penyewa, m.nama_mobil FROM penyewaan p LEFT JOIN user u ON p.id_user = u.id_user LEFT JOIN mobil m ON p.id_mobil = m.id_mobil ORDER BY p.tanggal_sewa DESC");
                $no2 = 1;
                while($l = mysqli_fetch_assoc($laporan)){
                ?>
                    <tr>
                        <td><?= $no2++; ?></td>
                        <td><?= htmlspecialchars($l['nama_penyewa'] ?: 'Unknown'); ?></td>
                        <td><?= htmlspecialchars($l['nama_mobil'] ?: 'Mobil dihapus'); ?></td>
                        <td><?= (int) $l['jumlah_sewa']; ?></td>
                        <td><?= htmlspecialchars($l['tanggal_sewa']); ?></td>
                    </tr>
                <?php }
                if(mysqli_num_rows($laporan) === 0){ ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada transaksi peminjaman.</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
