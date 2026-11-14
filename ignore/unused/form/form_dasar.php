<?php
$judul = 'Form Dasar';
$halaman = 'form';
require dirname(__DIR__, 2) . '/includes/header.php';

$sudahKirim = isset($_POST['kirim']);
$nama = trim($_POST['nama'] ?? '');
$produk = $_POST['produk'] ?? 'Beras 5 kg';
$jumlah = (int) ($_POST['jumlah'] ?? 1);
?>

<p class="tag">Dasar Form</p>
<h1>Form + Tabel Paling Sederhana</h1>
<p class="lead">Form adalah tempat mengisi data. Tabel adalah tempat menampilkan data. Di halaman ini keduanya digabung.</p>

<div class="card">
  <h2>1. Form: tempat mengisi data</h2>
  <form method="post" action="form_dasar.php">
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
    <button class="btn" type="submit" name="kirim" value="1">Kirim dan Tampilkan di Tabel</button>
  </form>
</div>

<div class="card">
  <h2>2. Tabel: tempat menampilkan data</h2>
  <table>
    <thead>
      <tr>
        <th>Nama</th>
        <th>Produk</th>
        <th>Jumlah</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($sudahKirim): ?>
        <tr>
          <td><?= htmlspecialchars($nama) ?></td>
          <td><?= htmlspecialchars($produk) ?></td>
          <td><?= $jumlah ?></td>
        </tr>
      <?php else: ?>
        <tr>
          <td colspan="3">Belum ada data. Isi form di atas, lalu klik tombol.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<div class="task">
  <h3>// CATATAN</h3>
  <p>Perhatikan: <code>name="nama"</code> menjadi kunci <code>$_POST['nama']</code>. Tanpa <code>name</code>, data tidak ikut terkirim.</p>
</div>

<?php require dirname(__DIR__, 2) . '/includes/footer.php'; ?>
