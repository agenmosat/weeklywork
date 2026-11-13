<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$error = '';
$name = '';
$price = '';
$stock = '';
$category = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim((string) ($_POST['price'] ?? ''));
    $stock = trim((string) ($_POST['stock'] ?? ''));
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = 'nama produk wajib diisi.';
    } elseif (mb_strlen($name) > 100) {
        $error = 'nama produk maksimal 100 karakter.';
    } elseif ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $error = 'harga wajib angka >= 0.';
    } elseif ($stock === '' || !ctype_digit($stock)) {
        $error = 'stok wajib angka bulat >= 0.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'INSERT INTO products (name, price, stock, category, description) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'sdiss', $name, $price, $stock, $category, $description);
        $ok = mysqli_stmt_execute($stmt);
        if ($ok) {
            header('Location: product.php?status=tambah');
            exit;
        }
        $error = 'gagal menyimpan produk.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud product - add</title>
    <link rel="stylesheet" href="../../style.css?v=3">
</head>
<body class="marketpage themelight">

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <div class="topnav">
            <a href="../../dashboard.php" class="navlink">store</a>
            <a href="../../admin.php" class="navlink">admin</a>
            <a href="../../admin.php?logout=1" class="navlink">logout</a>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="../../admin.php" class="sidelink">dashboard</a>
            <a href="product.php" class="sidelink">product</a>
            <a href="../cashier/cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">crud product - add</div>
            <div class="emptybox formbox">
                <div class="sidetitle">form tambah produk</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="addproduct.php" class="form">
                    <div class="formrow">
                        <label class="formlabel" for="name">nama produk</label>
                        <input class="forminput" type="text" id="name" name="name" maxlength="100" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="price">harga</label>
                        <input class="forminput" type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($price) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="stock">stok</label>
                        <input class="forminput" type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($stock) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="category">kategori</label>
                        <input class="forminput" type="text" id="category" name="category" maxlength="50" value="<?= htmlspecialchars($category) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="description">deskripsi</label>
                        <textarea class="forminput" id="description" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                    <div class="btnwrap">
                        <a href="product.php" class="sidelink">batal</a>
                        <button type="submit" class="enterbtn">simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
