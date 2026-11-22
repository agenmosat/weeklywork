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
                    foreach ($filtered as $p): 
                        $isalatberat = in_array($p['category'], $alatberatlist);
                    ?>
                        <div class="item">
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

                <div class="scrollbox">
                    <button type="button" onclick="window.scrollTo(0,0)" class="scrollbtn">kembali ke atas &uarr;</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
