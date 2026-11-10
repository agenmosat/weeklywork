<?php
$judul = 'Tes koneksi database';
$halaman = 'koneksi';

require __DIR__ . '/config/database.php';
$judul = 'Tes Koneksi Database';
$halaman = '';
require __DIR__ . '/includes/header.php';
?>
<h1>Tes Koneksi Database</h1>
<?php if (!$koneksi): ?>
  <p class="result">Koneksi gagal: <?= htmlspecialchars(mysqli_connect_error()) ?></p>
<?php else: ?>
  <p class="result">Koneksi ke database <strong><?= htmlspecialchars(DB_NAME) ?></strong> berhasil. Server: <?= htmlspecialchars(mysqli_get_server_info($koneksi)) ?>.</p>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
?>
