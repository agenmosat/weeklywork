<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$error = '';
$name = '';
$price = '';
$stock = '';
$unit = 'buah';
$lokasi = 'Surabaya';
$rating = '4.9';
$terjual = '0';
$category = '';
$description = '';
$image = 'images/no-image.webp';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim($_POST['name'] ?? '');
    $price = trim((string) ($_POST['price'] ?? ''));
    $stock = trim((string) ($_POST['stock'] ?? ''));
    $unit = trim($_POST['unit'] ?? 'buah');
    $lokasi = trim($_POST['lokasi'] ?? 'Surabaya');
    $rating = trim((string) ($_POST['rating'] ?? '4.9'));
    $terjual = trim((string) ($_POST['terjual'] ?? '0'));
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if (isset($_FILES['imagefile']) && $_FILES['imagefile']['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES['imagefile']['name']);
        $targetdir = __DIR__ . '/../../images/';
        $targetfile = $targetdir . $filename;
        if (move_uploaded_file($_FILES['imagefile']['tmp_name'], $targetfile)) {
            $image = 'images/' . $filename;
        }
    }

    if ($image === '') {
        $image = 'images/glock17.png';
    } elseif (strpos($image, 'images/') !== 0 && !preg_match('#^https?://#', $image)) {
        $image = 'images/' . $image;
    }

    if ($name === '') {
        $error = 'nama produk wajib diisi.';
    } elseif (mb_strlen($name) > 100) {
        $error = 'nama produk maksimal 100 karakter.';
    } elseif ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $error = 'harga wajib angka >= 0.';
    } elseif ($stock === '' || !ctype_digit($stock)) {
        $error = 'stok wajib angka bulat >= 0.';
    } else {
        $colcheck = mysqli_query($koneksi, "SHOW COLUMNS FROM products LIKE 'image'");
        if ($colcheck && mysqli_num_rows($colcheck) === 0) {
            mysqli_query($koneksi, "ALTER TABLE products ADD COLUMN image VARCHAR(255) DEFAULT 'images/glock17.png'");
        }

        $stmt = mysqli_prepare($koneksi, 'INSERT INTO products (name, price, stock, unit, lokasi, rating, terjual, category, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if ($stmt) {
            $f_price = (float) $price;
            $i_stock = (int) $stock;
            $f_rating = (float) $rating;
            $i_terjual = (int) $terjual;
            mysqli_stmt_bind_param($stmt, 'sdisdsisss', $name, $f_price, $i_stock, $unit, $lokasi, $f_rating, $i_terjual, $category, $description, $image);
            $ok = mysqli_stmt_execute($stmt);
            if ($ok) {
                header('Location: product.php?status=tambah');
                exit;
            }
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
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">crud product - add</div>
            <div class="emptybox formbox">
                <div class="sidetitle">form tambah produk</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="addproduct.php" class="form" enctype="multipart/form-data">
                    <?= csrf_field() ?>
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
                        <label class="formlabel" for="unit">satuan</label>
                        <input class="forminput" type="text" id="unit" name="unit" maxlength="50" value="<?= htmlspecialchars($unit) ?>" placeholder="sak / buah / batang / m3">
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="lokasi">lokasi kota</label>
                        <input class="forminput" type="text" id="lokasi" name="lokasi" maxlength="50" value="<?= htmlspecialchars($lokasi) ?>" placeholder="Surabaya">
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="rating">rating</label>
                        <input class="forminput" type="number" id="rating" name="rating" step="0.1" min="0" max="5" value="<?= htmlspecialchars($rating) ?>" placeholder="4.9">
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="terjual">jumlah terjual</label>
                        <input class="forminput" type="number" id="terjual" name="terjual" min="0" value="<?= htmlspecialchars($terjual) ?>" placeholder="0">
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="category">kategori</label>
                        <input class="forminput" type="text" id="category" name="category" maxlength="50" value="<?= htmlspecialchars($category) ?>" required>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="description">deskripsi</label>
                        <textarea class="forminput" id="description" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="image">nama / path gambar</label>
                        <input class="forminput" type="text" id="image" name="image" placeholder="images/glock17.png" value="<?= htmlspecialchars($image) ?>">
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="imagefile">unggah gambar</label>
                        <input class="forminput" type="file" id="imagefile" name="imagefile">
                    </div>
                    <div class="btnwrap">
                        <a href="product.php" class="sidelink">batal</a>
                        <button type="submit" class="enterbtn">simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>

</body>
</html>
