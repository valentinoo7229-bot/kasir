<?php
session_start();
require_once 'database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];

    if ($aksi == 'tambah') {
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];
        $stmt = $koneksi->prepare("INSERT INTO kasir_pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $alamat, $telepon);
        
        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Pelanggan berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan pelanggan: " . $koneksi->error;
        }

    } elseif ($aksi == 'edit') {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $alamat = $_POST['alamat'];
        $telepon = $_POST['telepon'];
        $stmt = $koneksi->prepare("UPDATE kasir_pelanggan SET NamaPelanggan = ?, Alamat = ?, NomorTelepon = ? WHERE PelangganID = ?");
        $stmt->bind_param("sssi", $nama, $alamat, $telepon, $id);

        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Data pelanggan berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui pelanggan: " . $koneksi->error;
        }

    } elseif ($aksi == 'hapus') {
        $id = $_POST['id'];
        $stmt_cek = $koneksi->prepare("SELECT COUNT(*) as count FROM kasir_penjualan WHERE PelangganID = ?");
        $stmt_cek->bind_param("i", $id);
        $stmt_cek->execute();
        $data = $stmt_cek->get_result()->fetch_assoc();

        if ($data['count'] > 0) {
            $_SESSION['error'] = "Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi.";
        } else {
            $stmt = $koneksi->prepare("DELETE FROM kasir_pelanggan WHERE PelangganID = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['pesan'] = "Pelanggan berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus pelanggan: " . $koneksi->error;
            }
        }
    }

    header("Location: kelola-pelanggan.php");
    exit();
}
?>