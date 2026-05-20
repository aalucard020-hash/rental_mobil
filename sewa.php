<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penyewa'){
    header('Location: index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: dashboard_penyewa.php');
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$id_mobil = (int) ($_POST['id_mobil'] ?? 0);
$jumlah = (int) ($_POST['jumlah_sewa'] ?? 0);

if($id_mobil <= 0 || $jumlah <= 0){
    header('Location: dashboard_penyewa.php');
    exit;
}

$tanggal_kembali = trim($_POST['tanggal_kembali'] ?? '');
if($tanggal_kembali === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_kembali)){
    header('Location: dashboard_penyewa.php');
    exit;
}

if(strtotime($tanggal_kembali) < strtotime(date('Y-m-d'))){
    header('Location: dashboard_penyewa.php');
    exit;
}

$result = mysqli_query($conn, "SELECT jumlah FROM mobil WHERE id_mobil = '$id_mobil'");
$mobil = mysqli_fetch_assoc($result);

if(!$mobil || $jumlah > (int) $mobil['jumlah']){
    header('Location: dashboard_penyewa.php');
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

$due = mysqli_real_escape_string($conn, $tanggal_kembali);
mysqli_query($conn, "INSERT INTO penyewaan (id_user, id_mobil, jumlah_sewa, tanggal_sewa, tanggal_jatuh_tempo, denda) VALUES ('$id_user', '$id_mobil', '$jumlah', CURDATE(), '$due', 0)");
mysqli_query($conn, "UPDATE mobil SET jumlah = jumlah - '$jumlah' WHERE id_mobil = '$id_mobil'");

header('Location: dashboard_penyewa.php');
exit;
?>