<?php
$judul = 'Produk';
$halaman = 'produk';
require __DIR__ . '/includes/header.php';
?>

<p class="tag">Data Toko</p>
<h1>Daftar Produk</h1>
<p class="lead">Contoh tabel produk. Ganti barisnya dengan barang yang dijual tokomu.</p>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Kode</th>
        <th>Nama Produk</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>P001</td>
        <td>Beras 5 kg</td>
        <td>Rp 65.000</td>
        <td>20</td>
        <td><span class="pill ok">Tersedia</span></td>
      </tr>
      <tr>
        <td>P002</td>
        <td>Minyak Goreng 1 L</td>
        <td>Rp 18.000</td>
        <td>35</td>
        <td><span class="pill ok">Tersedia</span></td>
      </tr>
      <tr>
        <td>P003</td>
        <td>Gula Pasir 1 kg</td>
        <td>Rp 15.000</td>
        <td>28</td>
        <td><span class="pill ok">Tersedia</span></td>
      </tr>
      <tr>
        <td>P004</td>
        <td>Telur 1 kg</td>
        <td>Rp 28.000</td>
        <td>0</td>
        <td><span class="pill no">Habis</span></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="task">
  <h3>// TUGAS KONTEN</h3>
  <ol>
    <li>Tambah minimal 2 produk baru.</li>
    <li>Ubah harga dan stok sesuai data kelompokmu.</li>
    <li>Tandai produk yang stoknya habis dengan <code>pill no</code>.</li>
  </ol>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
