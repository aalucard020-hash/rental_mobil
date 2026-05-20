<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: index.php');
    exit;
}

$id_sewa = (int) ($_GET['id'] ?? 0);
if($id_sewa <= 0){
    header('Location: laporan_sewa.php');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM penyewaan WHERE id_sewa = '$id_sewa' AND tanggal_kembali IS NULL");
$sewa = mysqli_fetch_assoc($result);
if(!$sewa){
    header('Location: laporan_sewa.php');
    exit;
}

$checkDueColumn = mysqli_query($conn, "SHOW COLUMNS FROM penyewaan LIKE 'tanggal_jatuh_tempo'");
if(mysqli_num_rows($checkDueColumn) === 0){
    mysqli_query($conn, "ALTER TABLE penyewaan ADD COLUMN tanggal_jatuh_tempo DATE NULL AFTER tanggal_kembali");
}
$checkDendaColumn = mysqli_query($conn, "SHOW COLUMNS FROM penyewaan LIKE 'denda'");
if(mysqli_num_rows($checkDendaColumn) === 0){
    mysqli_query($conn, "ALTER TABLE penyewaan ADD COLUMN denda INT NULL AFTER tanggal_jatuh_tempo");
}

$denda = 0;
if(!empty($sewa['tanggal_jatuh_tempo'])){
    $dueDate = strtotime($sewa['tanggal_jatuh_tempo']);
    $today = strtotime(date('Y-m-d'));
    if($today > $dueDate){
        $lateDays = floor(($today - $dueDate) / 86400);
        $denda = $lateDays * 100000 * (int) $sewa['jumlah_sewa'];
    }
}

mysqli_query($conn, "UPDATE penyewaan SET tanggal_kembali = CURDATE(), denda = '$denda' WHERE id_sewa = '$id_sewa'");
mysqli_query($conn, "UPDATE mobil SET jumlah = jumlah + '" . (int) $sewa['jumlah_sewa'] . "' WHERE id_mobil = '" . (int) $sewa['id_mobil'] . "'");

header('Location: laporan_sewa.php');
exit;
?>