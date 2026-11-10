<?php
$judul = 'Form POST';
$halaman = 'form';
require dirname(__DIR__, 2) . '/includes/header.php';

$sudahKirim = isset($_POST['kirim']);
$nama = trim($_POST['nama'] ?? '');
$produk = $_POST['produk'] ?? '';
$jumlah = (int) ($_POST['jumlah'] ?? 0);
?>

<p class="tag">Contoh POST</p>
<h1>Form POST: Data Tidak Tampil di URL</h1>
<p class="lead">Isi form, lalu klik tombol. Perhatikan bahwa alamat browser tetap sama.</p>

<div class="card">
  <h2>Form Pemesanan</h2>
  <form method="post" action="form_post.php">
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
    <button class="btn" type="submit" name="kirim" value="1">Kirim dengan POST</button>
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
    <p>Alamat browser tetap <code>form_post.php</code>, tanpa tanda <code>?</code>. Data tidak terlihat di address bar.</p>
    <p>Isi <code>$_POST</code> yang diterima PHP:</p>
    <table>
      <thead>
        <tr>
          <th>Key</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($_POST as $key => $value): ?>
          <tr>
            <td><?= htmlspecialchars($key) ?></td>
            <td><?= htmlspecialchars((string) $value) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Efek Tombol POST</h2>
    <ol class="steps">
      <li>Browser mengirim data di body request.</li>
      <li>Alamat browser tidak berubah.</li>
      <li>PHP membaca data lewat <code>$_POST</code>.</li>
      <li>Jika halaman di-refresh, browser bisa meminta konfirmasi kirim ulang.</li>
    </ol>
  </div>
<?php endif; ?>

<div class="task">
  <h3>// CATATAN</h3>
  <p>POST lebih cocok untuk login, transaksi, dan data pribadi. POST bukan berarti otomatis aman; tetap butuh validasi di server dan HTTPS saat online.</p>
</div>

<?php require dirname(__DIR__, 2) . '/includes/footer.php'; ?>
