<?php
// Kode yang lebih aman, tidak akan error jika dipanggil dua kali
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fungsi untuk menandai menu yang aktif
function is_active($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page == $page) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Kasir Kopi Kenangan Senja' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="assets/logo.png" alt="Logo Kopi Kenangan Senja">
                <h1>Kopi Kenangan Senja</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="<?= is_active('dashboard.php'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="index.php" class="<?= is_active('index.php'); ?>"><i class="fas fa-cash-register"></i> Kasir</a></li>
                <li><a href="kelola-stok.php" class="<?= is_active('kelola-stok.php'); ?>"><i class="fas fa-boxes"></i> Stok</a></li>
                <li><a href="kelola-pelanggan.php" class="<?= is_active('kelola-pelanggan.php'); ?>"><i class="fas fa-users"></i> Pelanggan</a></li>
                <li><a href="riwayat-transaksi.php" class="<?= is_active('riwayat-transaksi.php'); ?>"><i class="fas fa-history"></i> Riwayat</a></li>
            </ul>
            <div class="user-info">
                <span>
                    <i class="fas fa-user-circle"></i> 
                    <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?> 
                </span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </nav>
    </header>
    <main class="container">