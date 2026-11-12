<?php
session_start();
require_once "items.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$cartcount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartcount += $qty;
    }
}

$filtered = $products;
$searchquery = isset($_GET['q']) ? trim($_GET['q']) : "";
$category = isset($_GET['cat']) ? trim($_GET['cat']) : "";
$sort = isset($_GET['sort']) ? $_GET['sort'] : "";

if ($searchquery != "") {
    $temp = [];
    foreach ($filtered as $id => $p) {
        if (stripos($p['name'], $searchquery) !== false || stripos($p['desc'], $searchquery) !== false) {
            $temp[$id] = $p;
        }
    }
    $filtered = $temp;
}

if ($category != "") {
    $temp = [];
    foreach ($filtered as $id => $p) {
        if (isset($p['category']) && $p['category'] === $category) {
            $temp[$id] = $p;
        }
    }
    $filtered = $temp;
}

if ($sort === "low") {
    $prices = array_column($filtered, 'price');
    array_multisort($prices, SORT_ASC, $filtered);
} elseif ($sort === "high") {
    $prices = array_column($filtered, 'price');
    array_multisort($prices, SORT_DESC, $filtered);
} elseif ($sort === "name") {
    $names = array_column($filtered, 'name');
    array_multisort($names, SORT_ASC, $filtered);
}

$catparam = "";
if ($category != "") {
    $catparam = "&cat=" . urlencode($category);
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

    <div class="topbar">
        <div class="brand">darkfonder.net</div>
        <form action="dashboard.php" method="get" class="searchbox">
            <input type="text" name="q" placeholder="search items..." class="searchinput" value="<?php echo htmlspecialchars($searchquery); ?>">
            <button type="submit" class="searchbtn">search</button>
        </form>
        <div class="topnav">
            <a href="dashboard.php" class="navlink">home</a>
            <a href="cart.php" class="navlink">cart (<?php echo $cartcount; ?>)</a>
            <a href="dashboard.php?logout=1" class="navlink">logout</a>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <div class="sidetitle">categories</div>
            <a href="dashboard.php" class="sidelink">all items</a>

            <div class="sidetitle">sort by</div>
            <a href="dashboard.php?sort=low<?php echo $catparam; ?>" class="sidelink">price: low to high</a>
            <a href="dashboard.php?sort=high<?php echo $catparam; ?>" class="sidelink">price: high to low</a>
            <a href="dashboard.php?sort=name<?php echo $catparam; ?>" class="sidelink">name: a to z</a>


        </div>

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
                            <a href="crud/product/product.php" class="itemname"><?php echo htmlspecialchars($p['name']); ?></a>
                            <div class="itemprice">$<?php echo number_format($p['price'], 2); ?></div>
                            <div class="detaildesc">stock: <?php echo $p['stock']; ?> units</div>
                            <div class="itemactions">
                                <a href="crud/product/product.php?id=<?php echo $p['id']; ?>" class="buybtn">view item</a>
                                <a href="cart.php?add=<?php echo $p['id']; ?>" class="cartbtn">add to cart</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<script>
function switchtheme(theme) {
    document.body.className = "marketpage " + theme;
    localStorage.setItem("theme", theme);
}
var savedtheme = localStorage.getItem("theme");
if (savedtheme) {
    document.body.className = "marketpage " + savedtheme;
    var sel = document.getElementById("themepick");
    if (sel) {
        sel.value = savedtheme;
    }
}
</script>

</body>
</html>
