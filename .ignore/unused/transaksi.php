<?php
$judul = 'Transaksi';
$halaman = 'transaksi';
require __DIR__ . '/includes/header.php';

$items = [
    ['nama' => 'Beras 5 kg', 'qty' => 2, 'harga' => 65000],
    ['nama' => 'Minyak Goreng 1 L', 'qty' => 1, 'harga' => 18000],
    ['nama' => 'Gula Pasir 1 kg', 'qty' => 3, 'harga' => 15000],
];
$total = 0;
?>

<p class="tag">Contoh Perhitungan</p>
<h1>Transaksi Penjualan</h1>
<p class="lead">Ubah isi array <code>$items</code> untuk mengganti barang yang dibeli.</p>

<div class="card">
  <table>
    <thead>
      <tr>
        <th>Produk</th>
        <th>Qty</th>
        <th>Harga</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <?php
        $subtotal = $item['qty'] * $item['harga'];
        $total += $subtotal;
        ?>
        <tr>
          <td><?= htmlspecialchars($item['nama']) ?></td>
          <td><?= $item['qty'] ?></td>
          <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
          <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">Total</td>
        <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
      </tr>
    </tfoot>
  </table>
</div>

<div class="task">
  <h3>// TUGAS KONTEN</h3>
  <ol>
    <li>Ganti nama barang, qty, dan harga di array <code>$items</code>.</li>
    <li>Pastikan total berubah otomatis.</li>
    <li>Tambah satu barang lagi, lalu cek hasilnya di browser.</li>
  </ol>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
