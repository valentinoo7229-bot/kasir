<?php
 $host = 'localhost';
 $username = 'root'; // Ganti jika berbeda
 $password = '';     // Ganti jika berbeda
 $database = 'db_kasir'; // Pastikan nama database ini benar

 $koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}
?>