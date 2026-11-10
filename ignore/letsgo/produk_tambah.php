<?php
require __DIR__ . '/config/database.php';

$error = [];
$kode = '';
$nama = '';
$hargaBeliInput = '';
$hargaJualInput = '';
$stokInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = strtoupper(trim($_POST['kode_produk'] ?? ''));
    $nama = trim($_POST['nama_produk'] ?? '');
    $hargaBeliInput = trim((string) ($_POST['harga_beli'] ?? ''));
    $hargaJualInput = trim((string) ($_POST['harga_jual'] ?? ''));
    $stokInput = trim((string) ($_POST['stok_awal'] ?? ''));

    if ($kode === '') {
        $error[] = 'Kode produk wajib diisi.';
    } elseif (!preg_match('/^[A-Z0-9-]{2,10}$/', $kode)) {
        $error[] = 'Kode produk 2-10 karakter, hanya huruf, angka, atau tanda minus.';
    }

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
        $cek = mysqli_prepare($koneksi, 'SELECT kode_produk FROM produk WHERE kode_produk = ?');

        if (!$cek) {
            $error[] = 'Tabel produk belum siap: ' . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param($cek, 's', $kode);
            mysqli_stmt_execute($cek);
            mysqli_stmt_store_result($cek);

            if (mysqli_stmt_num_rows($cek) > 0) {
                $error[] = 'Kode produk ' . $kode . ' sudah dipakai.';
            }
            mysqli_stmt_close($cek);
        }
    }

    if (count($error) === 0) {
        $hargaBeli = (float) $hargaBeliInput;
        $hargaJual = (float) $hargaJualInput;
        $stok = (int) $stokValid;

        $stmt = mysqli_prepare(
            $koneksi,
            'INSERT INTO produk (kode_produk, nama_produk, harga_beli, harga_jual, stok_awal)
             VALUES (?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            $error[] = 'Gagal menyiapkan query: ' . mysqli_error($koneksi);
        } else {
            mysqli_stmt_bind_param($stmt, 'ssddi', $kode, $nama, $hargaBeli, $hargaJual, $stok);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: produk.php?status=tambah');
                exit;
            }

            $error[] = 'Gagal menyimpan produk: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

$judul = 'Tambah Produk';
$halaman = 'produk';
$pakai_jquery = true;
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <div>
    <span class="badge text-bg-primary mb-2">CREATE</span>
    <h1 class="h3 mb-1">Tambah Produk</h1>
    <p class="text-secondary mb-0">Isi data produk, lalu simpan ke database.</p>
  </div>
  <a class="btn btn-outline-secondary" href="produk.php">Kembali</a>
</div>

<?php if (count($error) > 0): ?>
  <div class="alert alert-danger">
    <strong>Data belum bisa disimpan:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($error as $pesan): ?>
        <li><?= htmlspecialchars($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form id="form-produk" method="post" action="produk_tambah.php">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="kode_produk">Kode Produk</label>
          <input class="form-control" type="text" id="kode_produk" name="kode_produk"
                 maxlength="10" value="<?= htmlspecialchars($kode) ?>"
                 placeholder="Contoh: BRG021" required>
          <div class="form-text">2-10 karakter, huruf/angka/tanda minus.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label" for="nama_produk">Nama Produk</label>
          <input class="form-control" type="text" id="nama_produk" name="nama_produk"
                 maxlength="50" value="<?= htmlspecialchars($nama) ?>"
                 placeholder="Contoh: Kopi Sachet" required>
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
                 min="0" step="0.01" value="<?= htmlspecialchars($hargaBeliInput) ?>"
                 placeholder="2500" required>
        </div>

        <div class="col-md-4">
          <label class="form-label" for="harga_jual">Harga Jual (Rp)</label>
          <input class="form-control" type="number" id="harga_jual" name="harga_jual"
                 min="0" step="0.01" value="<?= htmlspecialchars($hargaJualInput) ?>"
                 placeholder="3500" required>
        </div>

        <div class="col-md-4">
          <label class="form-label" for="stok_awal">Stok Awal</label>
          <input class="form-control" type="number" id="stok_awal" name="stok_awal"
                 min="0" step="1" value="<?= htmlspecialchars($stokInput) ?>"
                 placeholder="20" required>
        </div>

        <div class="col-12">
          <label class="form-label">Ringkasan Laba</label>
          <div id="ringkasan-laba" class="alert alert-secondary mb-0">
            Isi harga beli dan harga jual untuk melihat laba.
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">Simpan Produk</button>
        <a class="btn btn-outline-secondary" href="produk.php">Batal</a>
      </div>
    </form>
  </div>
</div>

<div class="alert alert-info mt-4 mb-0">
  Ingin membaca langkahnya? Buka
  <a class="alert-link" href="panduan.php#produk-tambah">penjelasan Form Tambah</a>.
  jQuery membantu menghitung laba, menghitung nama, dan mencegah klik ganda.
  Untuk tugas kasir dan supplier, buka komentar di file
  <code>kasir_tambah.php</code> dan <code>supplier_tambah.php</code>.
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
