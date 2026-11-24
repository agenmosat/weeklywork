<?php
require __DIR__ . '/config/database.php';

$kode = strtoupper(trim($_GET['kode'] ?? $_POST['kode_produk'] ?? ''));
$error = [];
$produk = null;

if ($kode === '') {
    $error[] = 'Kode produk tidak dikirim. Pilih produk dari halaman daftar.';
} else {
    $stmt = mysqli_prepare(
        $koneksi,
        'SELECT kode_produk, nama_produk, harga_beli, harga_jual, stok_awal
         FROM produk WHERE kode_produk = ?'
    );

    if (!$stmt) {
        $error[] = 'Tabel produk belum siap: ' . mysqli_error($koneksi);
    } else {
        mysqli_stmt_bind_param($stmt, 's', $kode);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $produk = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);

        if (!$produk) {
            $error[] = 'Produk dengan kode ' . $kode . ' tidak ditemukan.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produk) {
    $stmt = mysqli_prepare($koneksi, 'DELETE FROM produk WHERE kode_produk = ?');

    if (!$stmt) {
        $error[] = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
    } else {
        mysqli_stmt_bind_param($stmt, 's', $kode);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: produk.php?status=hapus');
            exit;
        }

        $error[] = 'Gagal menghapus produk: ' . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}

$judul = 'Hapus Produk';
$halaman = 'produk';
$pakai_jquery = true;
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <span class="badge text-bg-danger mb-2">DELETE</span>
    <h1 class="h3 mb-1">Hapus Produk</h1>
    <p class="text-secondary mb-0">Baca data, lalu konfirmasi penghapusan.</p>
  </div>
  <a class="btn btn-outline-secondary" href="produk.php">Kembali</a>
</div>

<?php if (count($error) > 0): ?>
  <div class="alert alert-danger">
    <strong>Produk tidak bisa diproses:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($error as $pesan): ?>
        <li><?= htmlspecialchars($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($produk): ?>
  <div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Produk yang Akan Dihapus</div>
    <div class="card-body">
      <table class="table table-sm mb-0">
        <tbody>
          <tr>
            <th style="width: 180px;">Kode Produk</th>
            <td><code><?= htmlspecialchars($produk['kode_produk']) ?></code></td>
          </tr>
          <tr>
            <th>Nama Produk</th>
            <td><?= htmlspecialchars($produk['nama_produk']) ?></td>
          </tr>
          <tr>
            <th>Harga Beli</th>
            <td>Rp <?= number_format((float) $produk['harga_beli'], 0, ',', '.') ?></td>
          </tr>
          <tr>
            <th>Harga Jual</th>
            <td>Rp <?= number_format((float) $produk['harga_jual'], 0, ',', '.') ?></td>
          </tr>
          <tr>
            <th>Stok Awal</th>
            <td><?= (int) $produk['stok_awal'] ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="alert alert-danger">
    <strong>Peringatan:</strong> produk yang sudah dipakai dalam transaksi
    sebaiknya tidak dihapus. Di aplikasi nyata, gunakan status nonaktif
    (<em>soft delete</em>) agar riwayat penjualan tetap utuh.
  </div>

  <form id="form-hapus" method="post" action="produk_hapus.php"
        data-confirm="Yakin ingin menghapus produk <?= htmlspecialchars($produk['nama_produk'], ENT_QUOTES, 'UTF-8') ?>?">
    <input type="hidden" name="kode_produk" value="<?= htmlspecialchars($kode) ?>">
    <button class="btn btn-danger" type="submit">Ya, Hapus Produk</button>
    <a class="btn btn-outline-secondary" href="produk.php">Batal</a>
  </form>
  <p class="small text-secondary mt-3 mb-0">
    Penjelasan lengkap ada di
    <a href="panduan.php#produk-hapus">Panduan Produk bagian Hapus</a>.
  </p>
<?php else: ?>
  <a class="btn btn-primary" href="produk.php">Kembali ke Daftar Produk</a>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
