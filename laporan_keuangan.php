<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: index.php');
    exit;
}

$checkReturnColumn = mysqli_query($conn, "SHOW COLUMNS FROM penyewaan LIKE 'tanggal_kembali'");
if(mysqli_num_rows($checkReturnColumn) === 0){
    mysqli_query($conn, "ALTER TABLE penyewaan ADD COLUMN tanggal_kembali DATE NULL AFTER tanggal_sewa");
}
$checkDueColumn = mysqli_query($conn, "SHOW COLUMNS FROM penyewaan LIKE 'tanggal_jatuh_tempo'");
if(mysqli_num_rows($checkDueColumn) === 0){
    mysqli_query($conn, "ALTER TABLE penyewaan ADD COLUMN tanggal_jatuh_tempo DATE NULL AFTER tanggal_kembali");
}
$checkDendaColumn = mysqli_query($conn, "SHOW COLUMNS FROM penyewaan LIKE 'denda'");
if(mysqli_num_rows($checkDendaColumn) === 0){
    mysqli_query($conn, "ALTER TABLE penyewaan ADD COLUMN denda INT NULL AFTER tanggal_jatuh_tempo");
}

$where = [];
$fromDate = '';
$toDate = '';
if(!empty($_GET['from_date'])){
    $fromDate = mysqli_real_escape_string($conn, $_GET['from_date']);
    $where[] = "p.tanggal_sewa >= '$fromDate'";
}
if(!empty($_GET['to_date'])){
    $toDate = mysqli_real_escape_string($conn, $_GET['to_date']);
    $where[] = "p.tanggal_sewa <= '$toDate'";
}
$filterQuery = '';
if(count($where) > 0){
    $filterQuery = 'WHERE ' . implode(' AND ', $where);
}

$summarySql = "SELECT 
    COUNT(*) AS total_transaksi, 
    SUM(p.jumlah_sewa * m.harga_sewa) AS total_sewa, 
    SUM(COALESCE(p.denda,0)) AS total_denda, 
    SUM(p.jumlah_sewa * m.harga_sewa + COALESCE(p.denda,0)) AS total_pendapatan 
FROM penyewaan p 
LEFT JOIN mobil m ON p.id_mobil = m.id_mobil " . $filterQuery;
$summaryResult = mysqli_query($conn, $summarySql);
$summary = mysqli_fetch_assoc($summaryResult);

$query = "SELECT p.*, u.nama AS nama_penyewa, m.nama_mobil, m.harga_sewa AS harga_satuan, (p.jumlah_sewa * m.harga_sewa) AS total_sewa, COALESCE(p.denda,0) AS denda, (p.jumlah_sewa * m.harga_sewa + COALESCE(p.denda,0)) AS total_bayar FROM penyewaan p LEFT JOIN user u ON p.id_user = u.id_user LEFT JOIN mobil m ON p.id_mobil = m.id_mobil " . $filterQuery . " ORDER BY p.tanggal_sewa DESC";
$laporan = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="d-flex dashboard">
    <div class="sidebar glass">
        <h2>🚗 Rental</h2>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="data_mobil.php">Data Mobil</a>
        <a href="laporan_sewa.php">Laporan Sewa</a>
        <a href="laporan_keuangan.php" class="active">Laporan Keuangan</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="glass card-modern mb-4">
            <h3>Laporan Keuangan</h3>
            <p>Ringkasan pendapatan rental dan denda berdasarkan periode sewa.</p>
            <form method="GET" class="d-flex gap-2 flex-wrap mt-3">
                <div>
                    <label class="form-label text-white">Dari tanggal</label>
                    <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDate); ?>">
                </div>
                <div>
                    <label class="form-label text-white">Sampai tanggal</label>
                    <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate); ?>">
                </div>
                <div class="align-self-end">
                    <button type="submit" class="btn-modern">Filter</button>
                </div>
            </form>
        </div>

        <div class="glass card-modern mb-4">
            <div class="row text-white">
                <div class="col-md-4 mb-3">
                    <div class="glass card-modern p-3">
                        <h5>Total Transaksi</h5>
                        <p><?= (int) ($summary['total_transaksi'] ?? 0); ?></p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="glass card-modern p-3">
                        <h5>Total Pendapatan Sewa</h5>
                        <p>Rp <?= number_format((int) ($summary['total_sewa'] ?? 0)); ?></p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="glass card-modern p-3">
                        <h5>Total Denda</h5>
                        <p>Rp <?= number_format((int) ($summary['total_denda'] ?? 0)); ?></p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="glass card-modern p-3">
                        <h5>Total Pendapatan</h5>
                        <p>Rp <?= number_format((int) ($summary['total_pendapatan'] ?? 0)); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass card-modern">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total Sewa</th>
                        <th>Denda</th>
                        <th>Total Bayar</th>
                        <th>Tanggal Sewa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($laporan) > 0): ?>
                        <?php $no = 1; while($row = mysqli_fetch_assoc($laporan)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_penyewa'] ?: 'Unknown'); ?></td>
                                <td><?= htmlspecialchars($row['nama_mobil'] ?: 'Mobil dihapus'); ?></td>
                                <td><?= (int) $row['jumlah_sewa']; ?></td>
                                <td>Rp <?= number_format((int) $row['harga_satuan']); ?></td>
                                <td>Rp <?= number_format((int) $row['total_sewa']); ?></td>
                                <td>Rp <?= number_format((int) $row['denda']); ?></td>
                                <td>Rp <?= number_format((int) $row['total_bayar']); ?></td>
                                <td><?= htmlspecialchars($row['tanggal_sewa']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data keuangan untuk periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
