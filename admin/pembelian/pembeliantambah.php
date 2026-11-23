<?php
require_once __DIR__ . '/../guard.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/transaksihelper.php';

init_transaksi_tables($koneksi);

$error = '';

$supplier_res = mysqli_query($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier ORDER BY nama_supplier ASC');
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
    $id_products = $_POST['id_product'] ?? [];
    $qtys = $_POST['jumlah'] ?? ($_POST['qty'] ?? []);
    $prices = $_POST['harga_beli'] ?? ($_POST['harga'] ?? []);

    if (!is_array($id_products)) {
        $id_products = [$id_products];
        $qtys = [$qtys];
        $prices = [$prices];
    }

    if ($id_supplier <= 0) {
        $error = 'Please select a supplier.';
    } elseif (empty($id_products)) {
        $error = 'Please add at least one item.';
    } else {
        $items = [];
        $grand_total = 0;

        for ($i = 0; $i < count($id_products); $i++) {
            $pid = (int)($id_products[$i] ?? 0);
            $qty = (int)($qtys[$i] ?? 0);
            $harga = (float)($prices[$i] ?? 0);

            if ($pid > 0 && $qty > 0) {
                if ($harga <= 0) {
                    $qp = mysqli_query($koneksi, "SELECT price FROM products WHERE id = $pid");
                    $prod = mysqli_fetch_assoc($qp);
                    $harga = $prod ? (float)$prod['price'] : 0;
                }
                $subtotal = $harga * $qty;
                $grand_total += $subtotal;
                $items[] = [
                    'id_product' => $pid,
                    'qty' => $qty,
                    'harga_beli' => $harga,
                    'subtotal' => $subtotal
                ];
            }
        }

        if (!empty($items)) {
            mysqli_begin_transaction($koneksi);
            try {
                $no = buat_nomor($koneksi, 'PB', 'pembelian', 'nomor_transaksi');
                mysqli_query($koneksi, "INSERT INTO pembelian (nomor_transaksi, id_supplier, total, status) VALUES ('$no', $id_supplier, $grand_total, 'selesai')");
                $id_pembelian = (int)mysqli_insert_id($koneksi);

                foreach ($items as $it) {
                    $pid = (int)$it['id_product'];
                    $qty = (int)$it['qty'];
                    $hrg = (float)$it['harga_beli'];
                    $sub = (float)$it['subtotal'];
                    mysqli_query($koneksi, "INSERT INTO pembelian_detail (id_pembelian, id_product, harga_beli, qty, subtotal) VALUES ($id_pembelian, $pid, $hrg, $qty, $sub)");
                    catat_mutasi_stok($koneksi, $pid, $qty, 0, null, $no, 'Pembelian ' . $no);
                }

                mysqli_commit($koneksi);
                header('Location: pembelian.php?status=tambah');
                exit;
            } catch (Exception $e) {
                mysqli_rollback($koneksi);
                $error = 'Failed to save purchase transaction.';
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
    <title>add purchase - darkfonder.net</title>
    <link rel="stylesheet" href="../../assets/style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/../../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../../partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">add new purchase transaction</div>

            <?php if ($error !== ''): ?>
                <div class="field" style="color:#d9534f;margin-bottom:12px;font-weight:bold"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="emptybox">
                <form action="pembeliantambah.php" method="post" id="formtransaksi">
                    <?= csrf_field(); ?>

                    <div class="formgrid" style="margin-bottom:16px">
                        <div class="field">
                            <label for="idsupplier" class="fieldlabel">select supplier</label>
                            <select class="forminput" id="idsupplier" name="id_supplier" required>
                                <option value="">-- select supplier --</option>
                                <?php foreach ($supplier_list as $supp): ?>
                                    <option value="<?= (int)$supp['id_supplier'] ?>" <?= (isset($_POST['id_supplier']) && (int)$_POST['id_supplier'] === (int)$supp['id_supplier']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($supp['nama_supplier']) ?> (<?= htmlspecialchars($supp['kota'] ?? '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="sidetitle" style="margin-bottom:8px">items in purchase</div>

                    <table class="carttable" id="tabelitem">
                        <thead>
                            <tr class="cartheader">
                                <th class="cartcell">product</th>
                                <th class="cartcell">purchase price ($)</th>
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
                                                <?= htmlspecialchars($prod_item['name']) ?> (curr stock: <?= (int)$prod_item['stock'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="cartcell">
                                    <input class="forminput priceinput" type="number" step="0.01" name="harga_beli[]" value="0.00" min="0.01" oninput="hitungbaris(this)" required>
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
                        <button class="buybtn" type="submit">save purchase</button>
                        <a href="pembelian.php" class="sidelink">cancel</a>
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
                        <?= htmlspecialchars($prod_item['name']) ?> (curr stock: <?= (int)$prod_item['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="cartcell">
            <input class="forminput priceinput" type="number" step="0.01" name="harga_beli[]" value="0.00" min="0.01" oninput="hitungbaris(this)" required>
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
