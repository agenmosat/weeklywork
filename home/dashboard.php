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
        $img = !empty($row['image']) ? $row['image'] : "images/glock17.png";
        $filtered[] = [
            "id" => $id,
            "name" => $row['name'],
            "price" => (float)$row['price'],
            "stock" => (int)$row['stock'],
            "category" => $row['category'],
            "image" => $img,
            "desc" => $row['description']
        ];
    }
}
$pagetitle = "darkfonder.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <div class="heading">
                <?php
                if ($searchquery != "") {
                    echo "search results for: " . htmlspecialchars($searchquery);
                } elseif ($category != "") {
                    echo "category: " . htmlspecialchars($category);
                } else {
                    echo "all items";
                }
                ?>
            </div>

            <?php if (empty($filtered)): ?>
                <div class="emptybox">
                    <div class="sidetitle">no items found</div>
                    <div class="detaildesc">try searching for something else or browse all items.</div>
                    <a href="dashboard.php" class="buybtn">view all items</a>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($filtered as $p): ?>
                        <div class="item">
                            <span class="tagitem"><?php echo htmlspecialchars($p['category']); ?></span>
                            <div class="imagebox">
                                <img src="../<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="productimage">
                            </div>
                            <a href="product.php?id=<?php echo urlen($p['id']); ?>" class="itemname"><?php echo htmlspecialchars($p['name']); ?></a>
                            <div class="itemprice">$<?php echo number_format($p['price'], 2); ?></div>
                            <div class="detaildesc">stock: <?php echo $p['stock']; ?> units</div>
                            <div class="itemactions">
                                <a href="product.php?id=<?php echo urlen($p['id']); ?>" class="buybtn">view item</a>
                                <a href="cart.php?add=<?php echo urlen($p['id']); ?>" class="cartbtn">add to cart</a>
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
