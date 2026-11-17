<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/transaksi_helper.php';

init_transaksi_tables($koneksi);

$error = '';

$product_res = mysqli_query($koneksi, 'SELECT id, name, price, stock FROM products ORDER BY name ASC');
$product_list = [];
if ($product_res) {
    while ($p = mysqli_fetch_assoc($product_res)) {
        $product_list[] = $p;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $id_product = (int)($_POST['id_product'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 0);

    if ($nama_pelanggan === '') {
        $error = 'customer name is required.';
    } elseif ($id_product <= 0) {
        $error = 'please select a product.';
    } elseif ($qty <= 0) {
        $error = 'quantity must be at least 1.';
    } else {
        $stmtp = mysqli_prepare($koneksi, 'SELECT id, price, stock FROM products WHERE id = ?');
        mysqli_stmt_bind_param($stmtp, 'i', $id_product);
        mysqli_stmt_execute($stmtp);
        $prodres = mysqli_stmt_get_result($stmtp);
        $prod = mysqli_fetch_assoc($prodres);

        if (!$prod) {
            $error = 'product not found.';
        } elseif ($prod['stock'] < $qty) {
            $error = 'insufficient product stock (available: ' . $prod['stock'] . ').';
        } else {
            $harga = (float)$prod['price'];
            $subtotal = hitung_total_transaksi($harga, $qty);
            $nomor_transaksi = generate_nomor_transaksi('PJL');

            $stmtpj = mysqli_prepare($koneksi, 'INSERT INTO penjualan (nomor_transaksi, nama_pelanggan, total) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmtpj, 'ssd', $nomor_transaksi, $nama_pelanggan, $subtotal);
            if (mysqli_stmt_execute($stmtpj)) {
                $id_penjualan = mysqli_insert_id($koneksi);

                $stmtdet = mysqli_prepare($koneksi, 'INSERT INTO penjualan_detail (id_penjualan, id_product, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmtdet, 'iidid', $id_penjualan, $id_product, $harga, $qty, $subtotal);
                mysqli_stmt_execute($stmtdet);

                $stmtstock = mysqli_prepare($koneksi, 'UPDATE products SET stock = stock - ? WHERE id = ?');
                mysqli_stmt_bind_param($stmtstock, 'ii', $qty, $id_product);
                mysqli_stmt_execute($stmtstock);

                header('Location: penjualan.php?status=tambah');
                exit;
            } else {
                $error = 'failed to save sales transaction.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>add sale - darkfonder.net</title>
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
            <a href="penjualan.php" class="sidelink">sales</a>
            <a href="../pembelian/pembelian.php" class="sidelink">purchases</a>
            <a href="../checklist.php" class="sidelink">checklist</a>
        </div>

        <div class="content">
            <div class="heading">add sales transaction</div>
            <div class="emptybox formbox">
                <div class="sidetitle">sales form</div>

                <?php if ($error !== ''): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="penjualan_tambah.php" class="form" onsubmit="return confirmtransaksi();">
                    <?= csrf_field() ?>
                    <div class="formrow">
                        <label class="formlabel" for="nama_pelanggan">customer name</label>
                        <input class="forminput" type="text" id="nama_pelanggan" name="nama_pelanggan" required value="<?= htmlspecialchars($_POST['nama_pelanggan'] ?? '') ?>">
                    </div>

                    <div class="formrow">
                        <label class="formlabel" for="id_product">select product</label>
                        <select class="forminput" id="id_product" name="id_product" onchange="pilihbarang()" required>
                            <option value="">-- select product --</option>
                            <?php foreach ($product_list as $prod_item): ?>
                                <option value="<?= (int)$prod_item['id'] ?>" data-harga="<?= (float)$prod_item['price'] ?>">
                                    <?= htmlspecialchars($prod_item['name']) ?> ($<?= number_format((float)$prod_item['price'], 2) ?> - stock: <?= (int)$prod_item['stock'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="formrow">
                        <label class="formlabel" for="harga">unit price ($)</label>
                        <input class="forminput" type="text" id="harga" name="harga" readonly value="0.00">
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
                        <button class="buybtn" type="submit">save transaction</button>
                        <a href="penjualan.php" class="sidelink">cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="../../assets/main.js"></script>
<script src="../../assets/transaksi.js"></script>

</body>
</html>
