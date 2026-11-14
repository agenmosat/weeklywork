<?php
require __DIR__ . '/config/database.php';

$cari = trim($_GET['cari'] ?? '');
$errorTabel = '';

if ($cari !== '') {
    $stmt = mysqli_prepare(
        $koneksi,
        'SELECT kode_produk, nama_produk, harga_beli, harga_jual, stok_awal
         FROM produk
         WHERE kode_produk LIKE ? OR nama_produk LIKE ?
         ORDER BY kode_produk'
    );

    if (!$stmt) {
        $errorTabel = mysqli_error($koneksi);
        $hasil = false;
    } else {
        $kataKunci = '%' . $cari . '%';
        mysqli_stmt_bind_param($stmt, 'ss', $kataKunci, $kataKunci);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    }
} else {
    $hasil = mysqli_query(
        $koneksi,
        'SELECT kode_produk, nama_produk, harga_beli, harga_jual, stok_awal
         FROM produk
         ORDER BY kode_produk'
    );

    if (!$hasil) {
        $errorTabel = mysqli_error($koneksi);
    }
}

$daftarProduk = [];
if ($hasil) {
    while ($baris = mysqli_fetch_assoc($hasil)) {
        $daftarProduk[] = $baris;
    }
}

$jumlahProduk = count($daftarProduk);
$totalStok = 0;
$stokMenipis = 0;

foreach ($daftarProduk as $baris) {
    $totalStok += (int) $baris['stok_awal'];
    if ((int) $baris['stok_awal'] <= 5) {
        $stokMenipis++;
    }
}

$pesanStatus = [
    'tambah' => ['success', 'Produk baru berhasil ditambahkan.'],
    'edit'   => ['success', 'Perubahan produk berhasil disimpan.'],
    'hapus'  => ['success', 'Produk berhasil dihapus.'],
];
$status = $_GET['status'] ?? '';

$judul = 'Daftar Produk';
$halaman = 'produk';
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <span class="badge text-bg-success mb-2">Contoh CRUD Lengkap</span>
    <h1 class="h3 mb-1">Daftar Produk</h1>
    <p class="text-secondary mb-0">Toko Maju Jaya &middot; database <code>db_penjualan</code></p>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-primary" href="panduan.php#produk">Penjelasan</a>
    <a class="btn btn-primary" href="produk_tambah.php">+ Tambah Produk</a>
  </div>
</div>

<?php if ($errorTabel !== ''): ?>
  <div class="alert alert-danger">
    <strong>Tabel produk belum bisa dibaca.</strong>
    <div class="small mt-1">Pesan MySQL: <?= htmlspecialchars($errorTabel) ?></div>
  </div>
<?php endif; ?>

<?php if (isset($pesanStatus[$status])): ?>
  <div class="alert alert-<?= $pesanStatus[$status][0] ?>">
    <?= htmlspecialchars($pesanStatus[$status][1]) ?>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-secondary small">Jumlah Produk</div>
        <div class="fs-3 fw-bold"><?= $jumlahProduk ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-secondary small">Total Stok</div>
        <div class="fs-3 fw-bold"><?= number_format($totalStok, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-secondary small">Stok Menipis (5 atau kurang)</div>
        <div class="fs-3 fw-bold text-danger"><?= $stokMenipis ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" action="produk.php" class="row g-2">
      <div class="col-md-8">
        <input class="form-control" type="text" name="cari"
               value="<?= htmlspecialchars($cari) ?>"
               placeholder="Cari kode atau nama produk...">
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Cari</button>
        <?php if ($cari !== ''): ?>
          <a class="btn btn-outline-secondary" href="produk.php">Reset</a>
        <?php endif; ?>
      </div>
    </form>
    <?php if ($cari !== ''): ?>
      <p class="text-secondary small mt-2 mb-0">
        Hasil pencarian untuk: <strong><?= htmlspecialchars($cari) ?></strong>
      </p>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Kode</th>
          <th>Nama Produk</th>
          <th class="text-end">Harga Beli</th>
          <th class="text-end">Harga Jual</th>
          <th class="text-end">Stok</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($jumlahProduk === 0): ?>
          <tr>
            <td colspan="7" class="text-center text-secondary py-4">
              <?= $cari !== '' ? 'Tidak ada produk yang cocok.' : 'Belum ada produk.' ?>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($daftarProduk as $produk): ?>
            <tr>
              <td><code><?= htmlspecialchars($produk['kode_produk']) ?></code></td>
              <td><?= htmlspecialchars($produk['nama_produk']) ?></td>
              <td class="text-end">Rp <?= number_format((float) $produk['harga_beli'], 0, ',', '.') ?></td>
              <td class="text-end">Rp <?= number_format((float) $produk['harga_jual'], 0, ',', '.') ?></td>
              <td class="text-end"><?= (int) $produk['stok_awal'] ?></td>
              <td>
                <?php if ((int) $produk['stok_awal'] <= 5): ?>
                  <span class="badge text-bg-danger">Menipis</span>
                <?php else: ?>
                  <span class="badge text-bg-success">Aman</span>
                <?php endif; ?>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="produk_edit.php?kode=<?= urlencode($produk['kode_produk']) ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger"
                   href="produk_hapus.php?kode=<?= urlencode($produk['kode_produk']) ?>">Hapus</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="alert alert-info mt-4 mb-0">
  Modul ini adalah contoh lengkap. Untuk tugas kasir dan supplier, buka
  <a class="alert-link" href="panduan.php#tugas-kasir">panduan bagian kasir</a>
  dan <a class="alert-link" href="panduan.php#tugas-supplier">bagian supplier</a>.
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
