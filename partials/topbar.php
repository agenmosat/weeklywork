<?php
$topbarcartcount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $topbarcartcount += $qty;
    }
}
$topbarquery = isset($_GET['q']) ? trim($_GET['q']) : "";
$topbarbase = '';
if (file_exists('config/database.php')) {
    $topbarbase = '';
} elseif (file_exists('../config/database.php')) {
    $topbarbase = '../';
} elseif (file_exists('../../config/database.php')) {
    $topbarbase = '../../';
}
?>
<div class="topbar">
    <div class="brand">darkfonder.net</div>
    <form action="<?php echo $topbarbase; ?>home/dashboard.php" method="get" class="searchbox">
        <input type="text" name="q" placeholder="search items..." class="searchinput" value="<?php echo htmlspecialchars($topbarquery); ?>">
        <button type="submit" class="searchbtn">search</button>
    </form>
    <div class="themeswitcher">
        <span class="themelabel">theme</span>
        <select id="themepick" class="themeselect" onchange="switchtheme(this.value)">
            <option value="themelight">light</option>
            <option value="themedark">dark</option>
        </select>
    </div>
    <div class="topnav">
        <a href="<?php echo $topbarbase; ?>home/dashboard.php" class="navlink">store</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin"): ?>
            <a href="<?php echo $topbarbase; ?>admin/admin.php" class="navlink">admin</a>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === "cashier"): ?>
            <a href="<?php echo $topbarbase; ?>admin/cashier/cashier.php" class="navlink">cashier</a>
        <?php endif; ?>
        <a href="<?php echo $topbarbase; ?>home/cart.php" class="navlink">cart (<?php echo $topbarcartcount; ?>)</a>
        <a href="<?php echo $topbarbase; ?>logout.php" class="navlink">logout</a>
    </div>
</div>
