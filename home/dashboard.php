<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$searchquery = isset($_GET['q']) ? trim($_GET['q']) : "";
$category = isset($_GET['cat']) ? trim($_GET['cat']) : "";
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : "";
$searchterm = "%" . $searchquery . "%";

if ($searchquery !== "" && $category !== "") {
    $wherestr = " WHERE (name LIKE ? OR description LIKE ?) AND category = ?";
} elseif ($searchquery !== "") {
    $wherestr = " WHERE (name LIKE ? OR description LIKE ?)";
} elseif ($category !== "") {
    $wherestr = " WHERE category = ?";
} else {
    $wherestr = "";
}

$orderstr = " ORDER BY id";
if ($sort === "low") {
    $orderstr = " ORDER BY price ASC";
} elseif ($sort === "high") {
    $orderstr = " ORDER BY price DESC";
} elseif ($sort === "name") {
    $orderstr = " ORDER BY name ASC";
}

$datasql = "SELECT * FROM products" . $wherestr . $orderstr;

if ($searchquery !== "" && $category !== "") {
    $stmt = mysqli_prepare($koneksi, $datasql);
    mysqli_stmt_bind_param($stmt, "sss", $searchterm, $searchterm, $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} elseif ($searchquery !== "") {
    $stmt = mysqli_prepare($koneksi, $datasql);
    mysqli_stmt_bind_param($stmt, "ss", $searchterm, $searchterm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} elseif ($category !== "") {
    $stmt = mysqli_prepare($koneksi, $datasql);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, $datasql);
}

$filtered = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int)$row['id'];
        $img = !empty($row['image']) ? $row['image'] : "images/no-image.webp";
        $filtered[] = [
            "id" => $id,
            "name" => $row['name'],
            "price" => (float)$row['price'],
            "stock" => (int)$row['stock'],
            "unit" => !empty($row['unit']) ? $row['unit'] : "buah",
            "lokasi" => !empty($row['lokasi']) ? $row['lokasi'] : "Surabaya",
            "rating" => isset($row['rating']) ? (float)$row['rating'] : 4.9,
            "terjual" => isset($row['terjual']) ? (int)$row['terjual'] : 0,
            "category" => $row['category'],
            "image" => $img,
            "desc" => $row['description']
        ];
    }
}
$pagetitle = "katalog bahan bangunan - fivefonders.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <?php if ($searchquery === "" && $category === ""): ?>
                <div class="bannerbox" id="herobanner">
                    <div class="bannerslider">
                        <div class="bannerslide active bannerslideone">
                            <span class="bannertag">promo proyek konstruksi</span>
                            <div class="bannertitle">Semen, Bata &amp; Material Bangunan Berkualitas</div>
                            <div class="bannerdesc">Penyedia resmi bahan bangunan standar SNI untuk proyek rumah, gedung, dan infrastruktur dengan jaminan harga grosir langsung dari distributor.</div>
                            <a href="#katalog" class="bannerbtn" onclick="document.querySelector('.heading').scrollIntoView({behavior:'smooth'}); return false;">belanja sekarang &rarr;</a>
                        </div>
                        <div class="bannerslide bannerslidetwo">
                            <span class="bannertag">sewa alat berat</span>
                            <div class="bannertitle">Excavator, Crane &amp; Bulldozer Siap Kerja</div>
                            <div class="bannerdesc">Rental alat berat harian, mingguan, dan bulanan lengkap dengan operator berpengalaman, bahan bakar, dan jaminan servis di lapangan.</div>
                            <a href="dashboard.php?cat=Excavator" class="bannerbtn">lihat katalog alat berat &rarr;</a>
                        </div>
                        <div class="bannerslide bannerslidethree">
                            <span class="bannertag">armada pengiriman toko</span>
                            <div class="bannertitle">Kirim Cepat Langsung ke Titik Proyek Anda</div>
                            <div class="bannerdesc">Armada truk dump dan pick-up toko siap antar material tepat waktu ke seluruh area Surabaya, Sidoarjo, Gresik, dan sekitarnya.</div>
                            <a href="about.php" class="bannerbtn">informasi armada toko &rarr;</a>
                        </div>
                    </div>
                    <button type="button" class="bannerprev" id="bannerprev" aria-label="sebelumnya">&lsaquo;</button>
                    <button type="button" class="bannernext" id="bannernext" aria-label="selanjutnya">&rsaquo;</button>
                    <div class="bannerdots">
                        <button type="button" class="bannerdot active" data-slide="0" aria-label="slide 1"></button>
                        <button type="button" class="bannerdot" data-slide="1" aria-label="slide 2"></button>
                        <button type="button" class="bannerdot" data-slide="2" aria-label="slide 3"></button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="heading">
                <?php
                if ($searchquery != "") {
                    echo "hasil pencarian: " . htmlspecialchars($searchquery);
                } elseif ($category != "") {
                    echo "kategori: " . htmlspecialchars($category);
                } else {
                    echo "semua produk";
                }
                ?>
            </div>

            <?php if (empty($filtered)): ?>
                <div class="emptybox">
                    <div class="sidetitle">produk tidak ditemukan</div>
                    <div class="detaildesc">coba cari dengan kata kunci lain atau lihat seluruh katalog produk kami.</div>
                    <a href="dashboard.php" class="buybtn">lihat semua produk</a>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php 
                    $alatberatlist = ['Excavator', 'Crane', 'Bulldozer', 'Wheel Loader', 'Forklift', 'Dump Truck', 'Concrete Mixer', 'Compactor', 'Motor Grader', 'Asphalt Paver', 'Concrete Pump', 'Genset'];
                    $itemidx = 0;
                    $initiallimit = 14;
                    foreach ($filtered as $p): 
                        $isalatberat = in_array($p['category'], $alatberatlist);
                        $ishidden = ($itemidx >= $initiallimit);
                        $itemidx++;
                    ?>
                        <div class="item <?php echo $ishidden ? 'loadmorehidden' : ''; ?>">
                            <div class="imagebox">
                                <img src="../<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="productimage" onerror="this.src='../images/no-image.webp'">
                            </div>
                            <a href="product.php?id=<?php echo urlen($p['id']); ?>" class="itemname"><?php echo htmlspecialchars($p['name']); ?></a>
                            <div class="itemprice">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?><?php echo $isalatberat ? ' <span class="unitlabel">/ unit</span>' : ''; ?></div>
                            <div class="itemmeta">
                                <span class="itemcity"><?php echo htmlspecialchars($p['lokasi']); ?></span> &bull; 
                                <span class="itemrating">&#9733; <?php echo number_format($p['rating'], 1); ?></span> &bull; 
                                <span class="itemsold">terjual <?php echo number_format($p['terjual'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="detaildesc"><?php echo $isalatberat ? 'unit sewa siap operasi' : 'stok: ' . $p['stock'] . ' ' . htmlspecialchars($p['unit']); ?></div>
                            <div class="itemactions">
                                <?php if ($isalatberat): ?>
                                    <a href="product.php?id=<?php echo urlen($p['id']); ?>" class="buybtn">ajukan sewa</a>
                                <?php else: ?>
                                    <a href="cart.php?add=<?php echo urlen($p['id']); ?>" class="buybtn">+ keranjang</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($filtered) > $initiallimit): ?>
                    <div class="loadmorebox" id="loadmorebox">
                        <button type="button" class="loadmorebtn" id="loadmorebtn" onclick="showmoreitems()">tampilkan lebih banyak</button>
                        <div class="loadmoreinfo" id="loadmoreinfo" style="display:none">semua produk telah ditampilkan</div>
                    </div>
                <?php endif; ?>

                <div class="scrollbox">
                    <button type="button" onclick="window.scrollTo(0,0)" class="scrollbtn">kembali ke atas &uarr;</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
