-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Bulan Mei 2026 pada 04.42
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_mobil`
--

DELIMITER $$
--
-- Fungsi
--
CREATE DEFINER=`root`@`localhost` FUNCTION `status_mobil` (`jumlah` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
   DECLARE hasil VARCHAR(20);
   IF jumlah <= 0 THEN
       SET hasil = 'Tidak Tersedia';
   ELSE
       SET hasil = 'Tersedia';
   END IF;
   RETURN hasil;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `laporan_pengembalian`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `laporan_pengembalian` (
`id_kembali` int(11)
,`nama` varchar(100)
,`nama_mobil` varchar(100)
,`tanggal_kembali` date
,`terlambat` int(11)
,`denda` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `laporan_penyewaan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `laporan_penyewaan` (
`id_sewa` int(11)
,`nama` varchar(100)
,`nama_mobil` varchar(100)
,`jumlah_sewa` int(11)
,`tanggal_sewa` date
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `nama_mobil` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `kondisi` varchar(50) DEFAULT NULL,
  `harga_sewa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `nama_mobil`, `jumlah`, `kondisi`, `harga_sewa`) VALUES
(1, 'Toyota Avanza', 3, 'Baik', 350000),
(2, 'Honda Brio', 3, 'Baik', 300000),
(3, 'Daihatsu Xenia', 4, 'Baik', 320000),
(4, 'Toyota Innova', 2, 'Sangat Baik', 500000),
(6, 'Lamborghini', 4, 'Baik', 600000),
(7, 'BMW', 10, 'Sangat Baik', 1000000),
(8, 'ferari', 2, 'Baik', 1000000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_kembali` int(11) NOT NULL,
  `id_sewa` int(11) DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `terlambat` int(11) DEFAULT NULL,
  `denda` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penyewaan`
--

CREATE TABLE `penyewaan` (
  `id_sewa` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_mobil` int(11) DEFAULT NULL,
  `jumlah_sewa` int(11) DEFAULT NULL,
  `tanggal_sewa` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `denda` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penyewaan`
--

INSERT INTO `penyewaan` (`id_sewa`, `id_user`, `id_mobil`, `jumlah_sewa`, `tanggal_sewa`, `tanggal_kembali`, `tanggal_jatuh_tempo`, `denda`) VALUES
(1, 2, 4, 0, '2026-05-20', '2026-05-20', NULL, NULL),
(2, 2, 1, 0, '2026-05-20', '2026-05-20', NULL, NULL),
(3, 2, 1, 2, '2026-05-20', '2026-05-20', NULL, NULL),
(4, 2, 1, 1, '2026-05-20', '2026-05-20', NULL, NULL),
(5, 2, 2, 1, '2026-05-20', '2026-05-20', NULL, NULL),
(6, 2, 1, 1, '2026-05-20', '2026-05-20', '2026-05-22', 0),
(7, 2, 1, 1, '2026-05-20', '2026-05-20', '2026-05-23', 0),
(8, 2, 6, 1, '2026-05-20', '2026-05-20', '2026-05-20', 0),
(9, 2, 6, 1, '2026-05-20', '2026-05-20', '2026-05-21', 0),
(10, 2, 6, 1, '2026-05-20', '2026-05-20', '2026-05-21', 0),
(11, 2, 4, 1, '2026-05-20', '2026-05-20', '2026-05-21', 0),
(12, 2, 2, 1, '2026-05-20', '2026-05-20', '2026-05-22', 0),
(13, 2, 6, 1, '2026-05-20', '2026-05-20', '2026-05-21', 0),
(14, 2, 3, 1, '2026-05-20', '2026-05-20', '2026-05-21', 0),
(15, 2, 3, 1, '2026-05-20', '2026-05-20', '2026-05-21', 0),
(16, 2, 7, 4, '2026-05-20', '2026-05-20', '2026-05-22', 0),
(17, 2, 1, 2, '2026-05-20', '2026-05-20', '2026-05-22', 0),
(18, 2, 8, 2, '2026-05-20', '2026-05-20', '2026-05-22', 0),
(19, 2, 8, 1, '2026-05-23', '2026-05-23', '2026-05-30', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','penyewa') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator', 'admin', '123', 'admin'),
(2, 'Budi Santoso', 'budi', '123', 'penyewa'),
(3, 'Andi Saputra', 'andi', '123', 'penyewa');

-- --------------------------------------------------------

--
-- Struktur untuk view `laporan_pengembalian`
--
DROP TABLE IF EXISTS `laporan_pengembalian`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_pengembalian`  AS SELECT `pg`.`id_kembali` AS `id_kembali`, `u`.`nama` AS `nama`, `m`.`nama_mobil` AS `nama_mobil`, `pg`.`tanggal_kembali` AS `tanggal_kembali`, `pg`.`terlambat` AS `terlambat`, `pg`.`denda` AS `denda` FROM (((`pengembalian` `pg` join `penyewaan` `p` on(`pg`.`id_sewa` = `p`.`id_sewa`)) join `user` `u` on(`p`.`id_user` = `u`.`id_user`)) join `mobil` `m` on(`p`.`id_mobil` = `m`.`id_mobil`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `laporan_penyewaan`
--
DROP TABLE IF EXISTS `laporan_penyewaan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_penyewaan`  AS SELECT `p`.`id_sewa` AS `id_sewa`, `u`.`nama` AS `nama`, `m`.`nama_mobil` AS `nama_mobil`, `p`.`jumlah_sewa` AS `jumlah_sewa`, `p`.`tanggal_sewa` AS `tanggal_sewa` FROM ((`penyewaan` `p` join `user` `u` on(`p`.`id_user` = `u`.`id_user`)) join `mobil` `m` on(`p`.`id_mobil` = `m`.`id_mobil`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_kembali`),
  ADD KEY `id_sewa` (`id_sewa`);

--
-- Indeks untuk tabel `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD PRIMARY KEY (`id_sewa`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_kembali` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penyewaan`
--
ALTER TABLE `penyewaan`
  MODIFY `id_sewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_sewa`) REFERENCES `penyewaan` (`id_sewa`);

--
-- Ketidakleluasaan untuk tabel `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD CONSTRAINT `penyewaan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `penyewaan_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
