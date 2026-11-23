<?php
require_once __DIR__ . '/guard.php';
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$orders = [];
$checktable = mysqli_query($koneksi, "SHOW TABLES LIKE 'pesanan'");
if ($checktable && mysqli_num_rows($checktable) > 0) {
    $orderres = mysqli_query($koneksi, "SELECT id, nomor_pesanan, nama_pembeli, total, metode_pembayaran, status_pembayaran, tanggal FROM pesanan ORDER BY id DESC LIMIT 10");
    if ($orderres) {
        while ($orow = mysqli_fetch_assoc($orderres)) {
            $orders[] = $orow;
        }
    }
}
$pagetitle = "admin - darkfonder.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebaradmin.php'; ?>

        <div class="content">
            <div class="heading">admin dashboard</div>
            <div class="emptybox">
                <div class="sidetitle">welcome superroot</div>
                <div class="detaildesc">admin dashboard access granted.</div>
            </div>

            <div class="emptybox" style="margin-top:14px">
                <div class="sidetitle">recent orders</div>
                <?php if (empty($orders)): ?>
                    <div class="detaildesc">no orders recorded yet.</div>
                <?php else: ?>
                    <table class="carttable">
                        <tr class="cartheader">
                            <th class="cartcell">id</th>
                            <th class="cartcell">order #</th>
                            <th class="cartcell">buyer</th>
                            <th class="cartcell">total</th>
                            <th class="cartcell">method</th>
                            <th class="cartcell">status</th>
                            <th class="cartcell">date</th>
                        </tr>
                        <?php foreach ($orders as $o): ?>
                            <tr class="cartrow">
                                <td class="cartcell"><?php echo $o['id']; ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['nomor_pesanan']); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['nama_pembeli']); ?></td>
                                <td class="cartcell">$<?php echo number_format((float)$o['total'], 2); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['metode_pembayaran']); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['status_pembayaran']); ?></td>
                                <td class="cartcell"><?php echo htmlspecialchars($o['tanggal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
