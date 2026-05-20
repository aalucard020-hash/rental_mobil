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
    <title>Laporan Sewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="d-flex dashboard">
    <div class="sidebar glass">
        <h2>🚗 Rental</h2>
        <a href="dashboard_admin.php">Dashboard</a>
        <a href="data_mobil.php">Data Mobil</a>
        <a href="laporan_sewa.php" class="active">Laporan Sewa</a>
        <a href="laporan_keuangan.php">Laporan Keuangan</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="glass card-modern mb-4">
            <h3>Laporan Peminjaman</h3>
            <p>Data penyewaan yang sudah tercatat.</p>
            <form method="GET" class="d-flex gap-2 flex-wrap mt-3">
                <div>
                    <label class="form-label text-white">Dari tanggal</label>
                    <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($_GET['from_date'] ?? ''); ?>">
                </div>
                <div>
                    <label class="form-label text-white">Sampai tanggal</label>
                    <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($_GET['to_date'] ?? ''); ?>">
                </div>
                <div class="align-self-end">
                    <button type="submit" class="btn-modern">Filter</button>
                </div>
            </form>
        </div>

        <div class="glass card-modern">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Jumlah Sewa</th>
                        <th>Tanggal Sewa</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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

                    $laporan = mysqli_query($conn, "SELECT p.*, u.nama AS nama_penyewa, m.nama_mobil FROM penyewaan p LEFT JOIN user u ON p.id_user = u.id_user LEFT JOIN mobil m ON p.id_mobil = m.id_mobil $filterQuery ORDER BY p.tanggal_sewa DESC");
                    $no = 1;
                    if(mysqli_num_rows($laporan) > 0){
                        while($row = mysqli_fetch_assoc($laporan)){
                            $denda = (int) ($row['denda'] ?? 0);
                            if($denda === 0 && empty($row['tanggal_kembali']) && !empty($row['tanggal_jatuh_tempo'])){
                                $due = strtotime($row['tanggal_jatuh_tempo']);
                                $now = strtotime(date('Y-m-d'));
                                if($now > $due){
                                    $lateDays = floor(($now - $due) / 86400);
                                    $denda = $lateDays * 100000 * (int) $row['jumlah_sewa'];
                                }
                            }
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_penyewa'] ?: 'Unknown'); ?></td>
                        <td><?= htmlspecialchars($row['nama_mobil'] ?: 'Mobil dihapus'); ?></td>
                        <td><?= (int) $row['jumlah_sewa']; ?></td>
                        <td><?= htmlspecialchars($row['tanggal_sewa']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_jatuh_tempo'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kembali'] ?? '-'); ?></td>
                        <td>
                            <?php if(!empty($row['tanggal_kembali'])): ?>
                                <span class="badge-available">Dikembalikan</span>
                            <?php else: ?>
                                <span class="badge-empty">Belum kembali</span>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?= number_format($denda); ?></td>
                        <td>
                            <?php if(empty($row['tanggal_kembali'])): ?>
                                <a href="admin_return_sewa.php?id=<?= (int) $row['id_sewa']; ?>" class="btn-modern">Tandai Kembali</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada laporan peminjaman.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
