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

if ($id <= 0) {
    header('Location: product.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = trim((string) ($_POST['price'] ?? ''));
    $stock = trim((string) ($_POST['stock'] ?? ''));
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($id <= 0 || $name === '') {
        $error = 'nama produk wajib diisi.';
    } elseif (mb_strlen($name) > 100) {
        $error = 'nama produk maksimal 100 karakter.';
    } elseif ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $error = 'harga wajib angka >= 0.';
    } elseif ($stock === '' || !ctype_digit($stock)) {
        $error = 'stok wajib angka bulat >= 0.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'UPDATE products SET name = ?, price = ?, stock = ?, category = ?, description = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'sdissi', $name, $price, $stock, $category, $description, $id);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: product.php?status=edit');
            exit;
        }
        $error = 'gagal mengubah produk.';
    }
} else {
    $stmt = mysqli_prepare($koneksi, 'SELECT id, name, price, stock, category, description FROM products WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
    if (!$row) {
        header('Location: product.php');
        exit;
    }
    $name = $row['name'];
    $price = $row['price'];
    $stock = $row['stock'];
    $category = $row['category'];
    $description = $row['description'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crud product - edit</title>
    <link rel="stylesheet" href="../../style.css?v=3">
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
            <div class="heading">crud product - edit #<?= (int) $id ?></div>
            <div class="emptybox formbox">
                <div class="sidetitle">form edit produk</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="editproduct.php?id=<?= (int) $id ?>" class="form">
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
                    <div class="btnwrap">
                        <a href="product.php" class="sidelink">batal</a>
                        <button type="submit" class="enterbtn">simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
function switchtheme(theme) {
    document.body.className = "marketpage " + theme;
    localStorage.setItem("theme", theme);
}
var savedtheme = localStorage.getItem("theme");
if (savedtheme) {
    document.body.className = "marketpage " + savedtheme;
    var sel = document.getElementById("themepick");
    if (sel) {
        sel.value = savedtheme;
    }
}
</script>

</body>
</html>
