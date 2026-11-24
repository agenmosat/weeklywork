<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/items.php';
require_once __DIR__ . '/includes/transaksihelper.php';

$directid = isset($_GET['id']) ? urlde($_GET['id']) : 0;
$directqty = isset($_GET['qty']) ?  max(1, (int)$_GET['qty']) : 1;

if (!isset($_SESSION['user'])) {
    $redirect_url = 'home/checkout.php' . ($directid > 0 ? '?id=' . urlen($directid) . '&qty=' . $directqty : '');
    $_SESSION['redirect_url'] = $redirect_url;
    header("Location: ../index.php?login=1&redirect=checkout");
    exit();
}
$source = ($directid > 0 && isset($products[$directid])) ? "direct" : "cart";

$result = hitung_pesanan($products, $source, $directid, $directqty, $_SESSION['cart'] ?? []);
$checkoutitems = $result['items'];
$subtotal = $result['subtotal'];
$shipping = $result['shipping'];
$total = $result['total'];
$directqty = $result['directqty'];

if (empty($checkoutitems)) {
    header("Location: cart.php");
    exit();
}

$pagetitle = "pengiriman pesanan - fivefonders.net";
require __DIR__ . '/partials/header.php';
?>

    <?php require __DIR__ . '/partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>

        <div class="content">
            <a href="cart.php" class="backlink">&laquo; kembali ke keranjang</a>
            <div class="heading">pengiriman pesanan</div>

            <form action="payment.php" method="post" class="formbox">
                <?= csrf_field(); ?>
                <input type="hidden" name="source" value="<?php echo htmlspecialchars($source); ?>">
                <?php if ($source === "direct") : ?>
                    <input type="hidden" name="direct_id" value="<?php echo $directid; ?>">
                    <input type="hidden" name="direct_qty" value="<?php echo $directqty; ?>">
                <?php endif; ?>

                <div class="detailbox">
                    <div class="detailinfo">
                        <div class="sidetitle">alamat pengiriman bahan bangunan</div>
                        <div class="formrow">
                            <label class="formlabel">nama penerima</label>
                            <input type="text" name="name" class="forminput" value="Budi Santoso" required>
                        </div>
                        <div class="formrow">
                            <label class="formlabel">alamat lengkap (rumah / proyek konstruksi)</label>
                            <input type="text" name="address" class="forminput" value="Jl. Raya Darmo No. 45" required>
                        </div>
                        <div class="formrow">
                            <label class="formlabel">kota dan kode pos</label>
                            <input type="text" name="city" class="forminput" value="Surabaya, 60241" required>
                        </div>

                        <div class="sidetitle">pilihan armada pengiriman</div>
                        <div class="formrow">
                            <label>
                                <input type="radio" name="shippingmethod" value="standard" checked> armada pengiriman toko (pick-up / truk) - Rp <?php echo number_format($shipping, 0, ',', '.'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="summarybox">
                        <div class="summarytitle">ringkasan pesanan</div>
                        <?php foreach ($checkoutitems as $ci) : ?>
                            <div class="summaryrow">
                                <span><?php echo htmlspecialchars($ci['name']); ?> (x<?php echo $ci['qty']; ?>)</span>
                                <span>Rp <?php echo number_format($ci['total'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="summaryrow">
                            <span>ongkos kirim armada</span>
                            <span>Rp <?php echo number_format($shipping, 0, ',', '.'); ?></span>
                        </div>
                        <div class="summarytotal">total pembayaran: Rp <?php echo number_format($total, 0, ',', '.'); ?></div>
                        <button type="submit" class="buybtn">lanjut ke pembayaran</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php require __DIR__ . '/partials/footer.php'; ?>
