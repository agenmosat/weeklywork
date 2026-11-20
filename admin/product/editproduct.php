<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
$error = '';
$name = '';
$price = '';
$stock = '';
$category = '';
$description = '';
$image = '';

if ($id <= 0) {
    header('Location: product.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = trim((string) ($_POST['price'] ?? ''));
    $stock = trim((string) ($_POST['stock'] ?? ''));
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

    if ($id <= 0 || $name === '') {
        $error = 'nama produk wajib diisi.';
    } elseif (mb_strlen($name) > 100) {
        $error = 'nama produk maksimal 100 karakter.';
    } elseif ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $error = 'harga wajib angka >= 0.';
    } elseif ($stock === '' || !ctype_digit($stock)) {
        $error = 'stok wajib angka bulat >= 0.';
    } else {

        $stmt = mysqli_prepare($koneksi, 'UPDATE products SET name = ?, price = ?, stock = ?, category = ?, description = ?, image = ? WHERE id = ?');
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sdisssi', $name, $price, $stock, $category, $description, $image, $id);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: product.php?status=edit');
                exit;
            }
        }
        $error = 'gagal mengubah produk.';
    }
} else {
    $stmt = mysqli_prepare($koneksi, 'SELECT * FROM products WHERE id = ?');
    $row = null;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $row = mysqli_fetch_assoc($res);
        }
    }
    if (!$row) {
        header('Location: product.php');
        exit;
    }
    $name = $row['name'];
    $price = $row['price'];
    $stock = $row['stock'];
    $category = $row['category'];
    $description = $row['description'];
    $image = $row['image'] ?? 'images/glock17.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud product - edit</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <div class="themeswitcher">
            <span class="themelabel">theme</span>
            <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
                <option value="themelight">light</option>
                <option value="themedark">dark</option>
            </select>
        </div>

        <div class="topnav">
            <a href="../../home/dashboard.php" class="navlink">store</a>
            <a href="../admin.php" class="navlink">admin</a>
            <form method="post" action="../../logout.php" class="logoutform">
                <button type="submit" class="logoutbtn">logout</button>
            </form>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="../admin.php" class="sidelink">dashboard</a>
            <a href="product.php" class="sidelink">product</a>
            <a href="../cashier/cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
        </div>

        <div class="content">
            <div class="heading">crud product - edit #<?= (int) $id ?></div>
            <div class="emptybox formbox">
                <div class="sidetitle">form edit produk</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="editproduct.php?id=<?= (int) $id ?>" class="form" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $id ?>">
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
                    <div class="formrow">
                        <label class="formlabel" for="image">nama / path gambar</label>
                        <input class="forminput" type="text" id="image" name="image" value="<?= htmlspecialchars($image) ?>">
                    </div>
                    <div class="formrow">
                        <label class="formlabel" for="imagefile">unggah gambar baru</label>
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
