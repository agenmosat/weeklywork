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

$nama = $produk['nama_produk'] ?? '';
$hargaBeliInput = $produk ? (string) $produk['harga_beli'] : '';
$hargaJualInput = $produk ? (string) $produk['harga_jual'] : '';
$stokInput = $produk ? (string) $produk['stok_awal'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $produk) {
    $nama = trim($_POST['nama_produk'] ?? '');
    $hargaBeliInput = trim((string) ($_POST['harga_beli'] ?? ''));
    $hargaJualInput = trim((string) ($_POST['harga_jual'] ?? ''));
    $stokInput = trim((string) ($_POST['stok_awal'] ?? ''));

    if ($nama === '') {
        $error[] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($nama) > 50) {
        $error[] = 'Nama produk maksimal 50 karakter.';
    }

    if ($hargaBeliInput === '' || !is_numeric($hargaBeliInput) || (float) $hargaBeliInput < 0) {
        $error[] = 'Harga beli harus berupa angka 0 atau lebih.';
    }

    if ($hargaJualInput === '' || !is_numeric($hargaJualInput) || (float) $hargaJualInput < 0) {
        $error[] = 'Harga jual harus berupa angka 0 atau lebih.';
    }

    $stokValid = filter_var($stokInput, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0],
    ]);
    if ($stokInput === '' || $stokValid === false) {
        $error[] = 'Stok awal harus berupa bilangan bulat 0 atau lebih.';
    }

    if (count($error) === 0) {
        $hargaBeli = (float) $hargaBeliInput;
        $hargaJual = (float) $hargaJualInput;
        $stok = (int) $stokValid;

        $stmt = mysqli_prepare(
            $koneksi,
            'UPDATE produk
             SET nama_produk = ?, harga_beli = ?, harga_jual = ?, stok_awal = ?
             WHERE kode_produk = ?'
        );

        if (!$stmt) {
            $error[] = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param($stmt, 'sddis', $nama, $hargaBeli, $hargaJual, $stok, $kode);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: produk.php?status=edit');
                exit;
            }

            $error[] = 'Gagal menyimpan perubahan: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

$judul = 'Edit Produk';
$halaman = 'produk';
$pakai_jquery = true;
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <span class="badge text-bg-primary mb-2">UPDATE</span>
    <h1 class="h3 mb-1">Edit Produk</h1>
    <p class="text-secondary mb-0">Ubah data produk. Kode produk tidak bisa diubah.</p>
  </div>
  <a class="btn btn-outline-secondary" href="produk.php">Kembali</a>
</div>

<?php if (count($error) > 0): ?>
  <div class="alert alert-danger">
    <strong>Perubahan belum bisa disimpan:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($error as $pesan): ?>
        <li><?= htmlspecialchars($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($produk): ?>
  <div class="card">
    <div class="card-body">
      <form id="form-produk" method="post" action="produk_edit.php">
        <input type="hidden" name="kode_produk" value="<?= htmlspecialchars($kode) ?>">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="kode_produk_tampil">Kode Produk</label>
            <input class="form-control" type="text" id="kode_produk_tampil"
                   value="<?= htmlspecialchars($kode) ?>" readonly>
            <div class="form-text">Primary key tidak bisa diubah.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="nama_produk">Nama Produk</label>
            <input class="form-control" type="text" id="nama_produk" name="nama_produk"
                   maxlength="50" value="<?= htmlspecialchars($nama) ?>" required>
            <div class="d-flex justify-content-between form-text">
              <span>Maksimal 50 karakter.</span>
              <span id="hitung-nama">0 / 50 karakter</span>
            </div>
            <div id="pratinjau-nama" class="border rounded bg-light p-2 mt-2 small text-secondary">
              Belum ada nama produk.
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label" for="harga_beli">Harga Beli (Rp)</label>
            <input class="form-control" type="number" id="harga_beli" name="harga_beli"
                   min="0" step="0.01" value="<?= htmlspecialchars($hargaBeliInput) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label" for="harga_jual">Harga Jual (Rp)</label>
            <input class="form-control" type="number" id="harga_jual" name="harga_jual"
                   min="0" step="0.01" value="<?= htmlspecialchars($hargaJualInput) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label" for="stok_awal">Stok Awal</label>
            <input class="form-control" type="number" id="stok_awal" name="stok_awal"
                   min="0" step="1" value="<?= htmlspecialchars($stokInput) ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Ringkasan Laba</label>
            <div id="ringkasan-laba" class="alert alert-secondary mb-0">
              Isi harga beli dan harga jual untuk melihat laba.
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
          <a class="btn btn-outline-secondary" href="produk.php">Batal</a>
        </div>
      </form>
      <p class="small text-secondary mt-3 mb-0">
        Penjelasan lengkap ada di
        <a href="panduan.php#produk-edit">Panduan Produk bagian Edit</a>.
      </p>
    </div>
  </div>
<?php else: ?>
  <a class="btn btn-primary" href="produk.php">Kembali ke Daftar Produk</a>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
