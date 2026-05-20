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

<title>Dashboard Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="d-flex dashboard">

    <!-- SIDEBAR -->

    <div class="sidebar glass">

        <h2>🚗 Rental</h2>

        <a href="dashboard_admin.php">Dashboard</a>
        <a href="data_mobil.php">Data Mobil</a>
        <a href="laporan_sewa.php">Laporan Sewa</a>
        <a href="laporan_keuangan.php">Laporan Keuangan</a>
        <a href="logout.php">Logout</a>

    </div>

    <!-- CONTENT -->

    <div class="main-content">

        <div class="glass card-modern mb-4">

            <h3>
                Halo,
                <?php echo $_SESSION['nama']; ?> 👋
            </h3>

            <p>Selamat datang di dashboard admin.</p>

        </div>

        <!-- FORM TAMBAH MOBIL -->

        <div class="glass card-modern mb-4">

            <h4 class="mb-4">Tambah Mobil</h4>

            <form method="POST">

                <div class="row">

                    <div class="col-md-3 mb-3">
                        <input type="text"
                        name="nama_mobil"
                        class="form-control"
                        placeholder="Nama Mobil">
                    </div>

                    <div class="col-md-2 mb-3">
                        <input type="number"
                        name="jumlah"
                        class="form-control"
                        placeholder="Jumlah">
                    </div>

                    <div class="col-md-3 mb-3">
                        <input type="text"
                        name="kondisi"
                        class="form-control"
                        placeholder="Kondisi">
                    </div>

                    <div class="col-md-2 mb-3">
                        <input type="number"
                        name="harga"
                        class="form-control"
                        placeholder="Harga">
                    </div>

                    <div class="col-md-2">
                        <button
                        name="simpan"
                        class="btn-modern">
                            Simpan
                        </button>
                    </div>

                </div>

            </form>

        </div>

        <?php

        if(isset($_POST['simpan'])){
            $nama = mysqli_real_escape_string($conn, trim($_POST['nama_mobil']));
            $jumlah = (int) ($_POST['jumlah'] ?? 0);
            $kondisi = mysqli_real_escape_string($conn, trim($_POST['kondisi']));
            $harga = (int) ($_POST['harga'] ?? 0);

            if($nama !== '' && $jumlah > 0 && $kondisi !== '' && $harga > 0){
                mysqli_query($conn, "INSERT INTO mobil (nama_mobil, jumlah, kondisi, harga_sewa) VALUES ('$nama', '$jumlah', '$kondisi', '$harga')");
                header('Location: dashboard_admin.php');
                exit;
            }
        }

        ?>

        <div class="glass card-modern">

            <h4 class="mb-4">Data Mobil</h4>

            <table class="table">

                <thead>
                    <tr>
                                <th>Mobil</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Kondisi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php

                $data = mysqli_query($conn,"
                SELECT *,
                status_mobil(jumlah) as status
                FROM mobil
                ");

                while($d=mysqli_fetch_array($data)){

                ?>

                <tr>

                    <td><?= $d['nama_mobil']; ?></td>

                    <td><?= $d['jumlah']; ?></td>

                    <td>

                    <?php
                    if($d['status']=="Tersedia"){
                        echo "<span class='badge-available'>Tersedia</span>";
                    }else{
                        echo "<span class='badge-empty'>Kosong</span>";
                    }
                    ?>

                    </td>

                    <td><?= htmlspecialchars($d['kondisi']); ?></td>

                    <td>
                        Rp <?= number_format($d['harga_sewa']); ?>
                    </td>
                    <td>
                        <a href="edit_mobil.php?id=<?= (int) $d['id_mobil']; ?>" class="btn-modern">Edit</a>
                        <a href="hapus_mobil.php?id=<?= (int) $d['id_mobil']; ?>" class="btn-modern btn-danger" onclick="return confirm('Hapus mobil ini?');">Hapus</a>
                    </td>

                </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>