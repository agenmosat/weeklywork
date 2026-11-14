<?php
$judul = 'Dashboard';
$halaman = 'dashboard';
require __DIR__ . '/includes/header.php';
?>

<p class="tag">Halaman Contoh</p>
<h1>Dashboard Toko</h1>
<p class="lead">Bagian atas, menu, dan footer sudah disiapkan. Kamu hanya perlu mengubah isi di area ini.</p>

<div class="grid">
  <div class="stat">
    <span class="stat-label">Produk</span>
    <strong class="stat-value">24</strong>
    <span class="stat-note">jenis barang</span>
  </div>
  <div class="stat">
    <span class="stat-label">Transaksi Hari Ini</span>
    <strong class="stat-value">18</strong>
    <span class="stat-note">struk tercetak</span>
  </div>
  <div class="stat">
    <span class="stat-label">Pendapatan</span>
    <strong class="stat-value">Rp 1.250.000</strong>
    <span class="stat-note">hari ini</span>
  </div>
</div>

<div class="card">
  <h2>Transaksi Terakhir</h2>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Pelanggan</th>
        <th>Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>Rina</td>
        <td>Rp 85.000</td>
        <td><span class="pill ok">Lunas</span></td>
      </tr>
      <tr>
        <td>2</td>
        <td>Budi</td>
        <td>Rp 42.000</td>
        <td><span class="pill ok">Lunas</span></td>
      </tr>
      <tr>
        <td>3</td>
        <td>Sari</td>
        <td>Rp 120.000</td>
        <td><span class="pill no">Belum Bayar</span></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="task">
  <h3>// TUGAS KONTEN</h3>
  <ol>
    <li>Ganti angka dan teks pada kartu ringkasan.</li>
    <li>Ganti isi tabel dengan data toko kelompokmu.</li>
    <li>Jangan ubah <code>includes/header.php</code> dan <code>includes/footer.php</code>.</li>
  </ol>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
