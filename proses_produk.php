<?php
session_start();
require_once 'database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'];

    if ($aksi == 'tambah') {
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $stmt = $koneksi->prepare("INSERT INTO kasir_produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $nama, $harga, $stok);
        
        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Produk berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan produk: " . $koneksi->error;
        }

    } elseif ($aksi == 'edit') {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $stmt = $koneksi->prepare("UPDATE kasir_produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?");
        $stmt->bind_param("sdii", $nama, $harga, $stok, $id);

        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Produk berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui produk: " . $koneksi->error;
        }

    } elseif ($aksi == 'hapus') {
        $id = $_POST['id'];
        $stmt_cek = $koneksi->prepare("SELECT COUNT(*) as count FROM kasir_detailpenjualan WHERE ProdukID = ?");
        $stmt_cek->bind_param("i", $id);
        $stmt_cek->execute();
        $data = $stmt_cek->get_result()->fetch_assoc();

        if ($data['count'] > 0) {
            $_SESSION['error'] = "Produk tidak dapat dihapus karena sudah ada dalam transaksi penjualan.";
        } else {
            $stmt = $koneksi->prepare("DELETE FROM kasir_produk WHERE ProdukID = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['pesan'] = "Produk berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus produk: " . $koneksi->error;
            }
        }
    }

    header("Location: kelola-stok.php");
    exit();
}
?>