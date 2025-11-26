<?php
 $page_title = "Manajemen Stok";
require_once 'database/koneksi.php';
require_once 'includes/header.php';

 $produk_query = $koneksi->query("SELECT * FROM kasir_produk ORDER BY NamaProduk ASC");
 $edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $koneksi->prepare("SELECT * FROM kasir_produk WHERE ProdukID = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
}
?>

    <h1>Manajemen Stok Produk</h1>
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert success"><?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-container">
        <h2><?= $edit_data ? 'Edit Produk' : 'Tambah Produk Baru' ?></h2>
        <form action="proses_produk.php" method="POST" class="form-manajemen">
            <input type="hidden" name="aksi" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['ProdukID'] ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="nama">Nama Produk</label>
                <input type="text" name="nama" id="nama" value="<?= $edit_data['NamaProduk'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" value="<?= $edit_data['Harga'] ?? '' ?>" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" name="stok" id="stok" value="<?= $edit_data['Stok'] ?? '' ?>" min="0" required>
            </div>
            <button type="submit" class="btn-submit"><?= $edit_data ? 'Update Produk' : 'Simpan Produk' ?></button>
            <?php if ($edit_data): ?>
                <a href="kelola-stok.php" class="btn-batal">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container">
        <h2>Daftar Produk</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($produk = $produk_query->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($produk['NamaProduk']) ?></td>
                    <td>Rp. <?= number_format($produk['Harga'], 2, ',', '.') ?></td>
                    <td><?= $produk['Stok'] ?></td>
                    <td class="aksi-column">
                        <a href="?edit=<?= $produk['ProdukID'] ?>" class="btn-edit">Edit</a>
                        <form action="proses_produk.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="id" value="<?= $produk['ProdukID'] ?>">
                            <button type="submit" class="btn-hapus">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<?php include 'includes/footer.php'; ?>