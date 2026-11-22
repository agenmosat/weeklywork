<?php
$headerbase = '';
if (file_exists('assets/style.css')) {
    $headerbase = '';
} elseif (file_exists('../assets/style.css')) {
    $headerbase = '../';
} elseif (file_exists('../../assets/style.css')) {
    $headerbase = '../../';
}
$pagetitle = $pagetitle ?? 'TB. Maju Jaya';
$bodyclass = $bodyclass ?? 'marketpage themelight';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pagetitle) ?></title>
    <link rel="stylesheet" href="<?= $headerbase ?>assets/style.css?v=3">
</head>
<body class="<?= htmlspecialchars($bodyclass) ?>">
