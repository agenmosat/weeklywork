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
    <a href="<?php echo $topbarbase; ?>home/dashboard.php" class="brand">darkfonder<span class="branddot">.</span>net</a>
    <form action="<?php echo $topbarbase; ?>home/dashboard.php" method="get" class="searchbox">
        <input type="text" name="q" placeholder="search building materials..." class="searchinput" value="<?php echo htmlspecialchars($topbarquery); ?>">
        <button type="submit" class="searchbtn">search</button>
    </form>
    <div class="topnav">
        <a href="<?php echo $topbarbase; ?>home/dashboard.php" class="navlink">store</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin"): ?>
            <a href="<?php echo $topbarbase; ?>admin/admin.php" class="navlink">admin</a>
        <?php endif; ?>
        <a href="<?php echo $topbarbase; ?>home/cart.php" class="navlink cartlink">cart <span class="cartcount"><?php echo $topbarcartcount; ?></span></a>
        <?php if (isset($_SESSION['user'])): ?>
            <span class="userbadge"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
        <?php endif; ?>
        <a href="<?php echo $topbarbase; ?>logout.php" class="navlink">logout</a>
        <div class="themeswitcher">
            <select id="themepick" class="themeselect" onchange="switchtheme(this.value)" aria-label="theme">
                <option value="themelight">light</option>
                <option value="themedark">dark</option>
            </select>
        </div>
    </div>
</div>
