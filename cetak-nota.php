<?php
// Tambahkan keamanan dan koneksi di bagian paling atas
session_start();
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke login
    header('Location: login.php');
    exit();
}

 $page_title = "Cetak Nota";
require_once 'database/koneksi.php';

// --- Sisanya kode Anda tetap sama di bawah ini ---
if (!isset($_GET['id'])) {
    header("Location: riwayat-transaksi.php");
    exit();
}

 $penjualan_id = $_GET['id'];
 $stmt_penjualan = $koneksi->prepare("SELECT p.*, pl.NamaPelanggan FROM kasir_penjualan p LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID WHERE p.PenjualanID = ?");
 $stmt_penjualan->bind_param("i", $penjualan_id);
 $stmt_penjualan->execute();
 $penjualan = $stmt_penjualan->get_result()->fetch_assoc();

if (!$penjualan) {
    echo "Nota tidak ditemukan.";
    exit();
}

 $stmt_detail = $koneksi->prepare("SELECT pr.NamaProduk, dp.JumlahProduk, dp.Harga, dp.Subtotal FROM kasir_detailpenjualan dp JOIN kasir_produk pr ON dp.ProdukID = pr.ProdukID WHERE dp.PenjualanID = ?");
 $stmt_detail->bind_param("i", $penjualan_id);
 $stmt_detail->execute();
 $result_detail = $stmt_detail->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="nota-wrapper">
        <div class="nota-container">
            <div class="nota-header">
                <div class="logo-area">
                    <i class="fas fa-mug-hot"></i>
                    <h1>Kopi Kenangan Senja</h1>
                </div>
                <p class="tagline">"Setiap Tegukan Adalah Cerita"</p>
                <hr>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Coffeeshop No. 1, Jakarta</p>
                <p><i class="fas fa-phone"></i> 021-12345678</p>
            </div>
            
            <div class="nota-body">
                <div class="nota-info">
                    <table>
                        <tr><td>No. Nota:</td><td>: #<?= str_pad($penjualan['PenjualanID'], 5, '0', STR_PAD_LEFT) ?></td></tr>
                        <tr><td>Kasir:</td><td>: <?= htmlspecialchars($_SESSION['nama_lengkap']) ?></td></tr>
                        <tr><td>Tanggal:</td><td>: <?= date('d M Y, H:i', strtotime($penjualan['TanggalPenjualan'])) ?></td></tr>
                        <tr><td>Pelanggan:</td><td>: <?= htmlspecialchars($penjualan['NamaPelanggan'] ?? 'Umum') ?></td></tr>
                    </table>
                </div>

                <hr class="dashed">

                <div class="nota-items">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="3">ITEM</th>
                                <th class="text-right">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = $result_detail->fetch_assoc()): ?>
                                <tr>
                                    <td colspan="3">
                                        <?= $item['NamaProduk'] ?><br>
                                        <small><?= number_format($item['Harga'], 0, ',', '.') ?> x <?= $item['JumlahProduk'] ?></small>
                                    </td>
                                    <td class="text-right"><?= number_format($item['Subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <hr class="dashed">

                <div class="nota-summary">
                    <table>
                        <tr><td>Subtotal:</td><td class="text-right"><?= number_format($penjualan['TotalHarga'], 0, ',', '.') ?></td></tr>
                        <tr><td>Tunai:</td><td class="text-right"><?= number_format($penjualan['UangBayar'], 0, ',', '.') ?></td></tr>
                        <tr class="grand-total"><td>Kembali:</td><td class="text-right"><?= number_format($penjualan['UangKembali'], 0, ',', '.') ?></td></tr>
                    </table>
                </div>
            </div>
            
            <div class="nota-footer">
                <hr>
                <p class="thank-you">Terima Kasih</p>
                <p class="visit-again">dan selamat menikmati</p>
                <br>
                <p class="social-info">www.kopikenangansenja.id</p>
            </div>
        </div>
        
        <div class="action-buttons">
            <?php if (isset($_GET['view'])): ?>
                <a href="riwayat-transaksi.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            <?php else: ?>
                <a href="index.php" class="btn btn-success"><i class="fas fa-cash-register"></i> Transaksi Baru</a>
            <?php endif; ?>
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Cetak Nota</button>
        </div>
    </div>

</body>
</html>