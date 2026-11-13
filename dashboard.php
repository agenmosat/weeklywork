<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/items.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$searchquery = isset($_GET['q']) ? trim($_GET['q']) : "";
$category = isset($_GET['cat']) ? trim($_GET['cat']) : "";
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : "";
$page = max(1, (int)($_GET['page'] ?? 1));
$perpage = 6;
$offset = ($page - 1) * $perpage;

$whereclauses = [];
$params = [];
$types = "";

if ($searchquery !== "") {
    $whereclauses[] = "(name LIKE ? OR description LIKE ?)";
    $searchterm = "%" . $searchquery . "%";
    $params[] = $searchterm;
    $params[] = $searchterm;
    $types .= "ss";
}

if ($category !== "") {
    $whereclauses[] = "category = ?";
    $params[] = $category;
    $types .= "s";
}

$wherestr = "";
if (!empty($whereclauses)) {
    $wherestr = " WHERE " . implode(" AND ", $whereclauses);
}

$orderstr = " ORDER BY id";
if ($sort === "low") {
    $orderstr = " ORDER BY price ASC";
} elseif ($sort === "high") {
    $orderstr = " ORDER BY price DESC";
} elseif ($sort === "name") {
    $orderstr = " ORDER BY name ASC";
}

$countsql = "SELECT COUNT(*) AS total FROM products" . $wherestr;
$totalitems = 0;
if (empty($params)) {
    $countres = mysqli_query($koneksi, $countsql);
    if ($countres) {
        $totalrow = mysqli_fetch_assoc($countres);
        $totalitems = (int) $totalrow['total'];
    }
} else {
    $stmt = mysqli_prepare($koneksi, $countsql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $countres = mysqli_stmt_get_result($stmt);
        if ($countres) {
            $totalrow = mysqli_fetch_assoc($countres);
            $totalitems = (int) $totalrow['total'];
        }
    }
}

$totalpages = max(1, (int)ceil($totalitems / $perpage));
if ($page > $totalpages) {
    $page = $totalpages;
    $offset = ($page - 1) * $perpage;
}

$datasql = "SELECT * FROM products" . $wherestr . $orderstr . " LIMIT " . (int)$perpage . " OFFSET " . (int)$offset;
if (empty($params)) {
    $result = mysqli_query($koneksi, $datasql);
} else {
    $stmt = mysqli_prepare($koneksi, $datasql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = false;
    }
}

$filtered = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int)$row['id'];
        $name = strtolower(trim($row['name']));
        $img = !empty($row['image']) ? $row['image'] : ($images[$name] ?? "images/glock17.png");
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

function pageurl($p) {
    $query = $_GET;
    $query['page'] = $p;
    return 'dashboard.php?' . http_build_query($query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>darkfonder.net</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="marketpage themelight">

    <?php require __DIR__ . '/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/sidebar.php'; ?>

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
                                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="productimage">
                            </div>
                            <a href="product.php?id=<?php echo $p['id']; ?>" class="itemname"><?php echo htmlspecialchars($p['name']); ?></a>
                            <div class="itemprice">$<?php echo number_format($p['price'], 2); ?></div>
                            <div class="detaildesc">stock: <?php echo $p['stock']; ?> units</div>
                            <div class="itemactions">
                                <a href="product.php?id=<?php echo $p['id']; ?>" class="buybtn">view item</a>
                                <a href="cart.php?add=<?php echo $p['id']; ?>" class="cartbtn">add to cart</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalpages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="<?php echo htmlspecialchars(pageurl($page - 1)); ?>" class="pagelink">&laquo; prev</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalpages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="pageactive"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars(pageurl($i)); ?>" class="pagelink"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($page < $totalpages): ?>
                            <a href="<?php echo htmlspecialchars(pageurl($page + 1)); ?>" class="pagelink">next &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

<script src="main.js"></script>

</body>
</html>
