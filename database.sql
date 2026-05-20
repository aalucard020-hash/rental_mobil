CREATE DATABASE rental_mobil;
USE rental_mobil;

-- tabel user
CREATE TABLE user (
   id_user INT AUTO_INCREMENT PRIMARY KEY,
   nama VARCHAR(100),
   username VARCHAR(50),
   password VARCHAR(100),
   role ENUM('admin','penyewa')
);

-- tabel mobil
CREATE TABLE mobil (
   id_mobil INT AUTO_INCREMENT PRIMARY KEY,
   nama_mobil VARCHAR(100),
   jumlah INT,
   kondisi VARCHAR(50),
   harga_sewa INT
);

-- tabel penyewaan
CREATE TABLE penyewaan (
   id_sewa INT AUTO_INCREMENT PRIMARY KEY,
   id_user INT,
   id_mobil INT,
   jumlah_sewa INT,
   tanggal_sewa DATE
);

-- data admin
INSERT INTO user(nama,username,password,role)
VALUES
('Administrator','admin','123','admin'),
('Budi','budi','123','penyewa');

-- procedure sewa mobil
DELIMITER //

CREATE PROCEDURE sewa_mobil(
   IN p_id_user INT,
   IN p_id_mobil INT,
   IN p_jumlah INT
)
BEGIN

   INSERT INTO penyewaan(
       id_user,
       id_mobil,
       jumlah_sewa,
       tanggal_sewa
   )
   VALUES(
       p_id_user,
       p_id_mobil,
       p_jumlah,
       CURDATE()
   );

   UPDATE mobil
   SET jumlah = jumlah - p_jumlah
   WHERE id_mobil = p_id_mobil;

END //

DELIMITER ;

-- function status mobil
DELIMITER //

CREATE FUNCTION status_mobil(jumlah INT)
RETURNS VARCHAR(20)

BEGIN

   DECLARE hasil VARCHAR(20);

   IF jumlah <= 0 THEN
       SET hasil = 'Tidak Tersedia';
   ELSE
       SET hasil = 'Tersedia';
   END IF;

   RETURN hasil;

END //

DELIMITER ;