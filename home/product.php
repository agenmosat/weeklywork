<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$id = isset($_GET['id']) ? urlde($_GET['id']) : 0;
$notfound = false;

if ($id <= 0 || !isset($products[$id])) {
    $notfound = true;
    http_response_code(404);
} else {
    $product = $products[$id];
}
$pagetitle = ($notfound ? "404 tidak ditemukan" : $product['name']) . " - fivefonders.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <a href="dashboard.php" class="backlink">&laquo; kembali ke katalog</a>

            <?php if ($notfound): ?>
                <div class="emptybox">
                    <div class="sidetitle">404 - produk tidak ditemukan</div>
                    <div class="detaildesc">produk yang anda cari tidak ditemukan atau telah dihapus dari katalog kami.</div>
                    <a href="dashboard.php" class="buybtn">kembali ke katalog</a>
                </div>
            <?php else: ?>
                <?php
                $alatberatlist = ['Excavator', 'Crane', 'Bulldozer', 'Wheel Loader', 'Forklift', 'Dump Truck', 'Concrete Mixer', 'Compactor', 'Motor Grader', 'Asphalt Paver', 'Concrete Pump', 'Genset'];
                $isalatberat = in_array($product['category'], $alatberatlist);
                ?>
                <div class="detailbox">
                    <div class="detailimage">
                        <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="productimage" onerror="this.src='../images/no-image.webp'">
                    </div>
                    <div class="detailinfo">
                        <div class="detailtitle"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="detailprice">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?><?php echo $isalatberat ? ' / unit' : ''; ?></div>
                        <div class="detaildesc"><?php echo htmlspecialchars($product['desc']); ?></div>
                        <div class="detailstatus">
                            <?php if ($product['stock'] > 0): ?>
                                <?php echo $isalatberat ? 'unit alat berat siap operasi (pool peralatan: Surabaya)' : 'stok tersedia: ' . $product['stock'] . ' ' . htmlspecialchars($product['unit'] ?? 'buah') . ' siap kirim dari gudang'; ?>
                            <?php else: ?>
                                stok saat ini habis
                            <?php endif; ?>
                        </div>

                        <?php if ($isalatberat): ?>
                            <div class="qtyform" style="margin-top:16px;">
                                <div class="detaildesc">untuk jadwal penyewaan unit alat berat dan penawaran proyek konstruksi, silakan ajukan sewa atau hubungi tim operasional kami.</div>
                                <div class="actionrow">
                                    <a href="cart.php?add=<?php echo urlen($product['id']); ?>" class="buybtn">ajukan sewa unit</a>
                                    <a href="about.php" class="cartbtn">kontak tim kami</a>
                                </div>
                            </div>
                        <?php elseif ($product['stock'] > 0): ?>
                            <form action="cart.php" method="post" class="qtyform">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars(urlen($product['id'])); ?>">
                                <div class="qtybox">
                                    <label class="formlabel">jumlah pesanan:</label>
                                    <input type="number" name="qty" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qtyinput" required>
                                </div>
                                <div class="actionrow">
                                    <button type="submit" name="action" value="buy" class="buybtn">beli sekarang</button>
                                    <button type="submit" name="action" value="cart" class="cartbtn">tambah ke keranjang</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
