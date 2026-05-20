<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($conn,
"SELECT * FROM user
WHERE username='$username'
AND password='$password'");

$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role'];

    if($data['role'] == 'admin'){
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard_penyewa.php");
    }

} else {

    echo "Login gagal";

}
?>