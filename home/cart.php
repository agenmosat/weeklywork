<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    if (isset($_POST['action'])) {
        $itemid = isset($_POST['id']) ? urlde($_POST['id']) : 0;
        $itemqty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;
        if (isset($products[$itemid])) {
            $availablestock = $products[$itemid]['stock'];
            if ($availablestock > 0) {
                if ($_POST['action'] === "buy") {
                    $buyqty = min($itemqty, $availablestock);
                    header("Location: checkout.php?id=" . urlen($itemid) . "&qty=" . $buyqty);
                    exit();
                }
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }
                $currentincart = $_SESSION['cart'][$itemid] ?? 0;
                $newcartqty = min($currentincart + $itemqty, $availablestock);
                if ($newcartqty > 0) {
                    $_SESSION['cart'][$itemid] = $newcartqty;
                }
            }
        }
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['updatecart']) && isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $qid => $newqty) {
            $qid = (int)$qid;
            $newqty = (int)$newqty;
            if (isset($products[$qid])) {
                $maxstock = $products[$qid]['stock'];
                if ($newqty > 0 && $maxstock > 0) {
                    $_SESSION['cart'][$qid] = min($newqty, $maxstock);
                } else {
                    unset($_SESSION['cart'][$qid]);
                }
            } else {
                unset($_SESSION['cart'][$qid]);
            }
        }
        header("Location: cart.php");
        exit();
    }
}

if (isset($_GET['add'])) {
    $addid = urlde($_GET['add']);
    if (isset($products[$addid])) {
        $availablestock = $products[$addid]['stock'];
        if ($availablestock > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $currentincart = $_SESSION['cart'][$addid] ?? 0;
            if ($currentincart < $availablestock) {
                $_SESSION['cart'][$addid] = $currentincart + 1;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $removeid = urlde($_GET['remove']);
    if (isset($_SESSION['cart'][$removeid])) {
        unset($_SESSION['cart'][$removeid]);
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

$cartcount = 0;
$subtotal = 0.0;
$cartitems = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cid => $qty) {
        if (isset($products[$cid])) {
            $p = $products[$cid];
            $realqty = min($qty, $p['stock']);
            if ($realqty <= 0) {
                unset($_SESSION['cart'][$cid]);
                continue;
            }
            $_SESSION['cart'][$cid] = $realqty;
            $itemtotal = $p['price'] * $realqty;
            $subtotal += $itemtotal;
            $cartcount += $realqty;
            $cartitems[] = [
                "id" => $p['id'],
                "name" => $p['name'],
                "price" => $p['price'],
                "stock" => $p['stock'],
                "qty" => $realqty,
                "total" => $itemtotal
            ];
        }
    }
}
$pagetitle = "keranjang belanja - TB. Maju Jaya";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <div class="heading">keranjang belanja</div>

            <?php if (empty($cartitems)): ?>
                <div class="emptybox">
                    <div class="sidetitle">keranjang belanja anda kosong</div>
                    <div class="detaildesc">belum ada bahan bangunan atau alat berat yang dipilih. silakan cari produk di katalog kami.</div>
                    <a href="dashboard.php" class="buybtn">lihat katalog</a>
                </div>
            <?php else: ?>
                <form action="cart.php" method="post" class="cartbox">
                    <?= csrf_field(); ?>
                    <table class="carttable">
                        <tr class="cartheader">
                            <th class="cartcell">nama produk</th>
                            <th class="cartcell">harga satuan</th>
                            <th class="cartcell">jumlah</th>
                            <th class="cartcell">subtotal</th>
                            <th class="cartcell">aksi</th>
                        </tr>
                        <?php foreach ($cartitems as $c): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?php echo htmlspecialchars($c['name']); ?></td>
                                <td class="cartcell">Rp <?php echo number_format($c['price'], 0, ',', '.'); ?></td>
                                <td class="cartcell">
                                    <input type="number" name="qty[<?php echo $c['id']; ?>]" value="<?php echo $c['qty']; ?>" min="1" max="<?php echo $c['stock']; ?>" class="qtyinput">
                                </td>
                                <td class="cartcell">Rp <?php echo number_format($c['total'], 0, ',', '.'); ?></td>
                                <td class="cartcell">
                                    <a href="cart.php?remove=<?php echo htmlspecialchars(urlen($c['id'])); ?>" class="removelink" onclick="return confirmremove();">hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="summarytotal">subtotal belanja: Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></div>

                    <div class="actionrow">
                        <a href="dashboard.php" class="backlink">&laquo; lanjut belanja</a>
                        <button type="submit" name="updatecart" class="cartbtn">perbarui keranjang</button>
                        <a href="cart.php?clear=1" class="cartbtn" onclick="return confirmclear();">kosongkan keranjang</a>
                        <a href="checkout.php" class="buybtn">lanjut ke pembayaran</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
