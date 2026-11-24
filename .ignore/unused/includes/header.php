<?php
$judul = $judul ?? 'KasirQu';
$halaman = $halaman ?? '';
$projectRoot = realpath(__DIR__ . '/..');
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$appBasePath = ($projectRoot && $documentRoot && str_starts_with($projectRoot, $documentRoot))
  ? str_replace(DIRECTORY_SEPARATOR, '/', substr($projectRoot, strlen($documentRoot)))
  : '';
$appBasePath = rtrim($appBasePath, '/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($judul) ?> - KasirQu</title>
  <link rel="stylesheet" href="<?= htmlspecialchars($appBasePath) ?>/assets/css/style.css">
</head>
<body>
  <header class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="<?= htmlspecialchars($appBasePath) ?>/index.php">KasirQu <small>/ Toko Maju Jaya</small></a>
      <nav class="menu">
        <a href="<?= htmlspecialchars($appBasePath) ?>/index.php" class="<?= $halaman === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="<?= htmlspecialchars($appBasePath) ?>/produk.php" class="<?= $halaman === 'produk' ? 'active' : '' ?>">Produk</a>
        <a href="<?= htmlspecialchars($appBasePath) ?>/transaksi.php" class="<?= $halaman === 'transaksi' ? 'active' : '' ?>">Transaksi</a>
        <a href="<?= htmlspecialchars($appBasePath) ?>/latihan/form/form.php" class="<?= $halaman === 'form' ? 'active' : '' ?>">Form</a>
      </nav>
    </div>
  </header>
  <main class="wrap">
