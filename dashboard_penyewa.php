<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'penyewa'){
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
?>

<!DOCTYPE html>
<html>
<head>

<title>Dashboard Penyewa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="container py-5">

    <div class="glass card-modern mb-4">

        <h2 class="mb-4">
            Halo <?= htmlspecialchars($_SESSION['nama']); ?> 👋
        </h2>

        <a href="logout.php" class="btn-modern">Logout</a>

    </div>

    <div class="glass card-modern">

        <h4 class="mb-4">Mobil untuk disewa</h4>
        <p class="mb-3">Ketentuan denda keterlambatan: Rp 100.000 per hari per unit. Harap pilih tanggal kembali tepat waktu untuk menghindari denda.</p>

        <table class="table">

            <thead>
                <tr>
                    <th>Mobil</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Sewa</th>
                </tr>
            </thead>

            <tbody>

            <?php

            $mobil = mysqli_query($conn, "SELECT * FROM mobil");

            while($m = mysqli_fetch_assoc($mobil)){
                $stok = isset($m['jumlah']) ? (int) $m['jumlah'] : 0;
            ?>

            <tr>

                <td><?= htmlspecialchars($m['nama_mobil']); ?></td>

                <td><?= number_format($stok); ?></td>

                <td>
                    Rp <?= number_format((int) $m['harga_sewa']); ?>
                </td>

                <td>
                    <?php if($m['jumlah'] > 0): ?>
                        <form action="sewa.php" method="POST" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="id_mobil" value="<?= (int) $m['id_mobil']; ?>">
                            <input type="number" name="jumlah_sewa" class="form-control" placeholder="Jumlah" min="1" max="<?= (int) $m['jumlah']; ?>" required>
                            <input type="date" name="tanggal_kembali" class="form-control" min="<?= date('Y-m-d'); ?>" required>
                            <button class="btn-modern">Sewa</button>
                        </form>
                    <?php else: ?>
                        <span class="badge-empty">Tidak tersedia</span>
                    <?php endif; ?>
                </td>

            </tr>

            <?php } ?>

            </tbody>

        </table>
        </div>

        <div class="glass card-modern mt-4">
            <h4 class="mb-4">Riwayat Sewa Anda</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mobil</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Tanggal Sewa</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Kembali</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $riwayat = mysqli_query($conn, "SELECT s.*, m.nama_mobil FROM penyewaan s LEFT JOIN mobil m ON s.id_mobil = m.id_mobil WHERE s.id_user = '" . (int) $_SESSION['id_user'] . "' ORDER BY s.tanggal_sewa DESC");
                    $no = 1;
                    while($r = mysqli_fetch_assoc($riwayat)){
                        $denda = 0;
                        if(!empty($r['denda'])){
                            $denda = (int) $r['denda'];
                        } elseif(empty($r['tanggal_kembali']) && !empty($r['tanggal_jatuh_tempo'])){
                            $due = strtotime($r['tanggal_jatuh_tempo']);
                            $now = strtotime(date('Y-m-d'));
                            if($now > $due){
                                $lateDays = floor(($now - $due) / 86400);
                                $denda = $lateDays * 100000 * (int) $r['jumlah_sewa'];
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($r['nama_mobil'] ?: 'Mobil tidak ditemukan'); ?></td>
                        <td><?= (int) $r['jumlah_sewa']; ?></td>
                        <td>
                            <?php if($r['tanggal_kembali']): ?>
                                <span class="badge-available">Dikembalikan</span>
                            <?php else: ?>
                                <span class="badge-empty">Belum kembali</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['tanggal_sewa']); ?></td>
                        <td><?= htmlspecialchars($r['tanggal_jatuh_tempo'] ?? '-'); ?></td>
                        <td><?= htmlspecialchars($r['tanggal_kembali'] ?? '-'); ?></td>
                        <td>Rp <?= number_format($denda); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>