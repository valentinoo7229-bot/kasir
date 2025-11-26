<?php
session_start();
require_once 'database/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pelanggan_id = isset($_POST['pelanggan_id']) && !empty($_POST['pelanggan_id']) ? (int)$_POST['pelanggan_id'] : NULL;
    $total_harga = (float)$_POST['total_bayar'];
    $uang_bayar = (float)$_POST['uang_bayar'];
    $uang_kembali = $uang_bayar - $total_harga;
    $cart_data = json_decode($_POST['cart_data'], true);

    if ($uang_bayar < $total_harga) {
        $_SESSION['error'] = "Error: Uang bayar tidak mencukupi.";
        header("Location: index.php");
        exit();
    }
    
    if (empty($cart_data)) {
        $_SESSION['error'] = "Error: Keranjang belanja kosong.";
        header("Location: index.php");
        exit();
    }

    $koneksi->begin_transaction();

    try {
        $query_penjualan = "INSERT INTO kasir_penjualan (PelangganID, TotalHarga, UangBayar, UangKembali) VALUES (?, ?, ?, ?)";
        $stmt_penjualan = $koneksi->prepare($query_penjualan);
        $stmt_penjualan->bind_param("iddd", $pelanggan_id, $total_harga, $uang_bayar, $uang_kembali);
        $stmt_penjualan->execute();
        $penjualan_id = $stmt_penjualan->insert_id;

        foreach ($cart_data as $item) {
            $produk_id = (int)$item['id'];
            $jumlah = (int)$item['qty'];
            $subtotal = (float)$item['subtotal'];

            $query_detail = "INSERT INTO kasir_detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Harga, Subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt_detail = $koneksi->prepare($query_detail);
            // Ambil harga saat ini dari produk untuk disimpan di detail
            $stmt_harga = $koneksi->prepare("SELECT Harga FROM kasir_produk WHERE ProdukID = ?");
            $stmt_harga->bind_param("i", $produk_id);
            $stmt_harga->execute();
            $harga_produk = $stmt_harga->get_result()->fetch_assoc()['Harga'];

            $stmt_detail->bind_param("iiidd", $penjualan_id, $produk_id, $jumlah, $harga_produk, $subtotal);
            $stmt_detail->execute();

            $query_stok = "UPDATE kasir_produk SET Stok = Stok - ? WHERE ProdukID = ?";
            $stmt_stok = $koneksi->prepare($query_stok);
            $stmt_stok->bind_param("ii", $jumlah, $produk_id);
            $stmt_stok->execute();
        }

        $koneksi->commit();
        header("Location: cetak-nota.php?id=" . $penjualan_id);
        exit();

    } catch (Exception $e) {
        $koneksi->rollback();
        $_SESSION['error'] = "Transaksi gagal: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
?>