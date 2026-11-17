<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/transaksi_helper.php';

init_transaksi_tables($koneksi);

$error = '';

$supplier_res = mysqli_query($koneksi, 'SELECT id, nama_supplier, kota FROM supplier ORDER BY nama_supplier ASC');
$supplier_list = [];
if ($supplier_res) {
    while ($s = mysqli_fetch_assoc($supplier_res)) {
        $supplier_list[] = $s;
    }
}

$product_res = mysqli_query($koneksi, 'SELECT id, name, price, stock FROM products ORDER BY name ASC');
$product_list = [];
if ($product_res) {
    while ($p = mysqli_fetch_assoc($product_res)) {
        $product_list[] = $p;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $id_supplier = (int)($_POST['id_supplier'] ?? 0);
    $id_product = (int)($_POST['id_product'] ?? 0);
    $harga_beli = (float)($_POST['harga_beli'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 0);

    if ($id_supplier <= 0) {
        $error = 'please select a supplier.';
    } elseif ($id_product <= 0) {
        $error = 'please select a product.';
    } elseif ($qty <= 0) {
        $error = 'quantity must be at least 1.';
    } elseif ($harga_beli <= 0) {
        $error = 'purchase price must be greater than 0.';
    } else {
        $subtotal = hitung_total_transaksi($harga_beli, $qty);
        $nomor_transaksi = generate_nomor_transaksi('PBL');

        $stmtpb = mysqli_prepare($koneksi, 'INSERT INTO pembelian (nomor_transaksi, id_supplier, total) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmtpb, 'sid', $nomor_transaksi, $id_supplier, $subtotal);
        if (mysqli_stmt_execute($stmtpb)) {
            $id_pembelian = mysqli_insert_id($koneksi);

            $stmtdet = mysqli_prepare($koneksi, 'INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmtdet, 'iidid', $id_pembelian, $id_product, $harga_beli, $qty, $subtotal);
            mysqli_stmt_execute($stmtdet);

            $stmtstock = mysqli_prepare($koneksi, 'UPDATE products SET stock = stock + ? WHERE id = ?');
            mysqli_stmt_bind_param($stmtstock, 'ii', $qty, $id_product);
            mysqli_stmt_execute($stmtstock);

            header('Location: pembelian.php?status=tambah');
            exit;
        } else {
            $error = 'failed to save purchase transaction.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add purchase - darkfonder.net</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../partials/topbar.php'; ?>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">admin menu</div>
            <a href="../admin.php" class="sidelink">dashboard</a>
            <a href="../product/product.php" class="sidelink">product</a>
            <a href="../cashier/cashier.php" class="sidelink">cashier</a>
            <a href="../supplier/supplier.php" class="sidelink">supplier</a>
            <a href="../penjualan/penjualan.php" class="sidelink">sales</a>
            <a href="pembelian.php" class="sidelink">purchases</a>
            <a href="../checklist.php" class="sidelink">checklist</a>
        </div>

        <div class="content">
            <div class="heading">add purchase transaction</div>
            <div class="emptybox formbox">
                <div class="sidetitle">purchase form</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="pembelian_tambah.php" class="form" onsubmit="return confirmtransaksi();">
                    <?= csrf_field() ?>
                    <div class="formrow">
                        <label class="formlabel" for="id_supplier">supplier</label>
                        <select class="forminput" id="id_supplier" name="id_supplier" required>
                            <option value="">-- select supplier --</option>
                            <?php foreach ($supplier_list as $sup_item): ?>
                                <option value="<?= (int)$sup_item['id'] ?>" <?= ((int)($_POST['id_supplier'] ?? 0) === (int)$sup_item['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sup_item['nama_supplier']) ?> (<?= htmlspecialchars($sup_item['kota']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="formrow">
                        <label class="formlabel" for="id_product">select product</label>
                        <select class="forminput" id="id_product" name="id_product" onchange="pilihbarang()" required>
                            <option value="">-- select product --</option>
                            <?php foreach ($product_list as $prod_item): ?>
                                <option value="<?= (int)$prod_item['id'] ?>" data-harga="<?= (float)$prod_item['price'] ?>">
                                    <?= htmlspecialchars($prod_item['name']) ?> (current stock: <?= (int)$prod_item['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="formrow">
                        <label class="formlabel" for="harga">unit purchase price ($)</label>
                        <input class="forminput" type="number" step="0.01" min="0.01" id="harga" name="harga_beli" value="0.00" oninput="hitungsubtotal()" required>
                    </div>

                    <div class="formrow">
                        <label class="formlabel" for="qty">quantity</label>
                        <input class="forminput" type="number" id="qty" name="qty" min="1" value="1" oninput="hitungsubtotal()" required>
                    </div>

                    <div class="formrow">
                        <label class="formlabel" for="subtotal">subtotal ($)</label>
                        <input class="forminput" type="text" id="subtotal" name="subtotal" readonly value="0.00">
                    </div>

                    <div class="actionrow">
                        <button class="buybtn" type="submit">save purchase</button>
                        <a href="pembelian.php" class="sidelink">cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>
<script src="../../assets/transaksi.js"></script>

</body>
</html>
