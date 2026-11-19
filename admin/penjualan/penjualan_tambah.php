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
    $id_products = $_POST['id_product'] ?? [];
    $qtys = $_POST['jumlah'] ?? ($_POST['qty'] ?? []);
    $prices = $_POST['harga'] ?? [];

    if (!is_array($id_products)) {
        $id_products = [$id_products];
        $qtys = [$qtys];
        $prices = [$prices];
    }

    if ($nama_pelanggan === '') {
        $error = 'Customer name is required.';
    } elseif (empty($id_products)) {
        $error = 'Please select at least one product.';
    } else {
        $grand_total = 0;
        $items = [];
        $stock_ok = true;

        for ($i = 0; $i < count($id_products); $i++) {
            $pid = (int)($id_products[$i] ?? 0);
            $qty = (int)($qtys[$i] ?? 0);
            $harga = (float)($prices[$i] ?? 0);

            if ($pid > 0 && $qty > 0) {
                $qp = mysqli_query($koneksi, "SELECT name, price, stock FROM products WHERE id = $pid");
                $prod = mysqli_fetch_assoc($qp);
                if (!$prod) {
                    $error = 'Product not found.';
                    $stock_ok = false;
                    break;
                }
                if ((int)$prod['stock'] < $qty) {
                    $error = 'Insufficient stock for ' . htmlspecialchars($prod['name']) . ' (available: ' . (int)$prod['stock'] . ').';
                    $stock_ok = false;
                    break;
                }
                if ($harga <= 0) {
                    $harga = (float)$prod['price'];
                }
                $subtotal = $harga * $qty;
                $grand_total += $subtotal;
                $items[] = [
                    'id_product' => $pid,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal
                ];
            }
        }

        if ($stock_ok && !empty($items)) {
            mysqli_begin_transaction($koneksi);
            try {
                $no = buat_nomor($koneksi, 'PJ', 'penjualan', 'nomor_transaksi');
                $nama_esc = mysqli_real_escape_string($koneksi, $nama_pelanggan);
                mysqli_query($koneksi, "INSERT INTO penjualan (nomor_transaksi, nama_pelanggan, total, status) VALUES ('$no', '$nama_esc', $grand_total, 'selesai')");
                $id_penjualan = (int)mysqli_insert_id($koneksi);

                foreach ($items as $it) {
                    $pid = (int)$it['id_product'];
                    $qty = (int)$it['qty'];
                    $hrg = (float)$it['harga'];
                    $sub = (float)$it['subtotal'];
                    mysqli_query($koneksi, "INSERT INTO penjualan_detail (id_penjualan, id_product, harga, qty, subtotal) VALUES ($id_penjualan, $pid, $hrg, $qty, $sub)");
                    catat_mutasi_stok($koneksi, $pid, 0, $qty, $no, null, 'Penjualan ' . $no);
                }

                mysqli_commit($koneksi);
                header('Location: penjualan.php?status=tambah');
                exit;
            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                $error = 'Failed to save transaction.';
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
    <title>add sales - darkfonder.net</title>
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
            <div class="sidetitle">reports</div>
            <a href="../laporan_penjualan.php" class="sidelink">sales report</a>
            <a href="../laporan_pembelian.php" class="sidelink">purchase report</a>
            <a href="../laporan_stok.php" class="sidelink">stock report</a>
            <a href="../laporan_laba.php" class="sidelink">profit report</a>
        </div>

        <div class="content">
            <div class="heading">add new sales transaction</div>

            <?php if ($error !== ''): ?>
                <div class="field" style="color:#d9534f;margin-bottom:12px;font-weight:bold"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="emptybox">
                <form action="penjualan_tambah.php" method="post" id="formtransaksi">
                    <?= csrf_field(); ?>

                    <div class="formgrid" style="margin-bottom:16px">
                        <div class="field">
                            <label for="namapelanggan" class="fieldlabel">customer name</label>
                            <input class="forminput" type="text" id="namapelanggan" name="nama_pelanggan" value="<?= htmlspecialchars($_POST['nama_pelanggan'] ?? '') ?>" placeholder="enter customer name..." required>
                        </div>
                    </div>

                    <div class="sidetitle" style="margin-bottom:8px">items in transaction</div>

                    <table class="carttable" id="tabelitem">
                        <thead>
                            <tr class="cartheader">
                                <th class="cartcell">product</th>
                                <th class="cartcell">unit price ($)</th>
                                <th class="cartcell">qty</th>
                                <th class="cartcell">subtotal ($)</th>
                                <th class="cartcell">action</th>
                            </tr>
                        </thead>
                        <tbody id="badanitem">
                            <tr class="cartrow itemrow">
                                <td class="cartcell">
                                    <select class="forminput prodselect" name="id_product[]" onchange="pilihitem(this)" required>
                                        <option value="">-- select product --</option>
                                        <?php foreach ($product_list as $prod_item): ?>
                                            <option value="<?= (int)$prod_item['id'] ?>" data-harga="<?= (float)$prod_item['price'] ?>" data-stock="<?= (int)$prod_item['stock'] ?>">
                                                <?= htmlspecialchars($prod_item['name']) ?> ($<?= number_format((float)$prod_item['price'], 2) ?> - stock: <?= (int)$prod_item['stock'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="cartcell">
                                    <input class="forminput priceinput" type="number" step="0.01" name="harga[]" value="0.00" readonly>
                                </td>
                                <td class="cartcell">
                                    <input class="forminput qtyinput" type="number" name="jumlah[]" min="1" value="1" oninput="hitungbaris(this)" required>
                                </td>
                                <td class="cartcell">
                                    <input class="forminput subtotalinput" type="text" name="subtotal[]" value="0.00" readonly>
                                </td>
                                <td class="cartcell">
                                    <button type="button" class="cartbtn btndel" onclick="hapusbaris(this)">remove</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="cartrow">
                                <td class="cartcell" colspan="3" style="text-align:right"><strong>grand total ($):</strong></td>
                                <td class="cartcell" colspan="2">
                                    <input class="forminput totalbox" type="text" id="grandtotal" name="grandtotal" readonly value="0.00">
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="actionrow">
                        <button type="button" class="enterbtn btnadd" onclick="tambahbaris()">+ add item</button>
                    </div>

                    <div class="actionrow" style="margin-top:16px">
                        <button class="buybtn" type="submit">save transaction</button>
                        <a href="penjualan.php" class="sidelink">cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<template id="templatebaris">
    <tr class="cartrow itemrow">
        <td class="cartcell">
            <select class="forminput prodselect" name="id_product[]" onchange="pilihitem(this)" required>
                <option value="">-- select product --</option>
                <?php foreach ($product_list as $prod_item): ?>
                    <option value="<?= (int)$prod_item['id'] ?>" data-harga="<?= (float)$prod_item['price'] ?>" data-stock="<?= (int)$prod_item['stock'] ?>">
                        <?= htmlspecialchars($prod_item['name']) ?> ($<?= number_format((float)$prod_item['price'], 2) ?> - stock: <?= (int)$prod_item['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="cartcell">
            <input class="forminput priceinput" type="number" step="0.01" name="harga[]" value="0.00" readonly>
        </td>
        <td class="cartcell">
            <input class="forminput qtyinput" type="number" name="jumlah[]" min="1" value="1" oninput="hitungbaris(this)" required>
        </td>
        <td class="cartcell">
            <input class="forminput subtotalinput" type="text" name="subtotal[]" value="0.00" readonly>
        </td>
        <td class="cartcell">
            <button type="button" class="cartbtn btndel" onclick="hapusbaris(this)">remove</button>
        </td>
    </tr>
</template>

<script src="../../assets/main.js"></script>
<script src="../../assets/transaksi.js"></script>

</body>
</html>
