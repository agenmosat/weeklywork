<?php
$judul = 'Hasil Form Latihan';
$halaman = 'form';
require dirname(__DIR__, 2) . '/includes/header.php';
$nama = trim($_GET['nama'] ?? '');
?>
<h1>Hasil Form Latihan</h1>
<div class="card">
  <?php if (isset($_GET['kirim'])): ?>
    <p>Nama: <strong><?= htmlspecialchars($nama) ?></strong></p>
  <?php else: ?>
    <p>Belum ada data yang dikirim. Silakan kembali ke form latihan.</p>
  <?php endif; ?>
  <a class="btn" href="form_latihan.php">Kembali ke Form</a>
</div>
<?php require dirname(__DIR__, 2) . '/includes/footer.php'; ?>
