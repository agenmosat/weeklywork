<?php
$judul = 'Form GET';
$halaman = 'form';
require dirname(__DIR__, 2) . '/includes/header.php';

$sudahKirim = isset($_GET['kirim']);
$nama = trim($_GET['nama'] ?? '');
$produk = $_GET['produk'] ?? '';
$jumlah = (int) ($_GET['jumlah'] ?? 0);
?>

<p class="tag">Contoh GET</p>
<h1>Form GET: Data Tampil di URL</h1>
<p class="lead">Isi form, lalu klik tombol. Perhatikan alamat browser setelah halaman dimuat ulang.</p>

<div class="card">
  <h2>Form Pemesanan</h2>
  <form method="get" action="form_get.php">
    <div class="form-grid">
      <div class="field">
        <label for="nama">Nama Pembeli</label>
        <input type="text" id="nama" name="nama" placeholder="Contoh: Rina" required>
      </div>
      <div class="field">
        <label for="produk">Produk</label>
        <select id="produk" name="produk">
          <option value="Beras 5 kg">Beras 5 kg</option>
          <option value="Minyak Goreng 1 L">Minyak Goreng 1 L</option>
          <option value="Gula Pasir 1 kg">Gula Pasir 1 kg</option>
          <option value="Telur 1 kg">Telur 1 kg</option>
        </select>
      </div>
      <div class="field">
        <label for="jumlah">Jumlah</label>
        <input type="number" id="jumlah" name="jumlah" min="1" value="1" required>
      </div>
    </div>
    <button class="btn" type="submit" name="kirim" value="1">Kirim dengan GET</button>
  </form>
</div>

<?php if ($sudahKirim): ?>
  <div class="result">
    <h2>Hasil yang Dibaca PHP</h2>
    <p>Nama: <strong><?= htmlspecialchars($nama) ?></strong></p>
    <p>Produk: <strong><?= htmlspecialchars($produk) ?></strong></p>
    <p>Jumlah: <strong><?= $jumlah ?></strong></p>
  </div>

  <div class="card">
    <h2>URL Setelah Tombol Diklik</h2>
    <p>Data form ikut pindah ke alamat browser:</p>
    <pre>form_get.php?nama=<?= urlencode($nama) ?>&amp;produk=<?= urlencode($produk) ?>&amp;jumlah=<?= $jumlah ?>&amp;kirim=1</pre>
    <p>Isi <code>$_GET</code> yang diterima PHP:</p>
    <table>
      <thead>
        <tr>
          <th>Key</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($_GET as $key => $value): ?>
          <tr>
            <td><?= htmlspecialchars($key) ?></td>
            <td><?= htmlspecialchars((string) $value) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Efek Tombol GET</h2>
    <ol class="steps">
      <li>Browser mengubah isi form menjadi query string.</li>
      <li>Halaman dimuat ulang dengan alamat yang berisi data.</li>
      <li>PHP membaca data lewat <code>$_GET</code>.</li>
      <li>URL bisa di-bookmark atau dibagikan ke orang lain.</li>
    </ol>
  </div>
<?php endif; ?>

<div class="task">
  <h3>// CATATAN</h3>
  <p>GET cocok untuk pencarian dan filter. Jangan memakai GET untuk password atau data pribadi karena datanya terlihat di URL.</p>
</div>

<?php require dirname(__DIR__, 2) . '/includes/footer.php';?>
