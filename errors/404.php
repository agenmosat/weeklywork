<?php
http_response_code(404);
require_once __DIR__ . '/../home/includes/session.php';

$pagetitle = "404 - halaman tidak ditemukan | fivefonders.net";
require __DIR__ . '/../home/partials/header.php';
require __DIR__ . '/../home/partials/topbar.php';
?>

<div class="errorsection">
    <div class="errorcontainer">
        <div class="errorgraphic">
            <svg class="erroricon" viewBox="0 0 120 120" width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="10" y="20" width="100" height="80" rx="8" fill="var(--bgsoft2)" stroke="var(--borderstrong)" stroke-width="3"/>
                <path d="M10 40H110" stroke="var(--borderstrong)" stroke-width="3"/>
                <circle cx="26" cy="30" r="4" fill="var(--danger)"/>
                <circle cx="40" cy="30" r="4" fill="var(--accent)"/>
                <circle cx="54" cy="30" r="4" fill="var(--success)"/>
                <path d="M42 62L78 78M78 62L42 78" stroke="var(--accent)" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <div class="errorbadge">kesalahan 404</div>
        </div>

        <div class="errorcode">404</div>
        <h1 class="errortitle">halaman tidak ditemukan</h1>
        <p class="errortext">
            maaf, halaman yang anda tuju tidak ditemukan atau sedang dalam proses renovasi. silakan periksa alamat url atau cari bahan bangunan yang anda butuhkan melalui kotak pencarian di bawah.
        </p>

        <form action="../home/dashboard.php" method="get" class="errorsearch">
            <input type="text" name="q" placeholder="cari semen, bata, besi, alat berat..." class="errorinput" required>
            <button type="submit" class="errorsearchbtn">cari</button>
        </form>

        <div class="erroractions">
            <a href="../home/dashboard.php" class="errorbtn">kembali ke katalog</a>
            <a href="../home/serving.php" class="errorbtnsec">pusat bantuan</a>
        </div>

        <div class="errorquick">
            <div class="errorquicktitle">tautan yang mungkin membantu:</div>
            <div class="errorquicklinks">
                <a href="../home/dashboard.php?cat=Semen" class="errorquickitem">semen &amp; mortar</a>
                <a href="../home/dashboard.php?cat=Bata" class="errorquickitem">bata &amp; batako</a>
                <a href="../home/dashboard.php?cat=Besi+%26+Baja" class="errorquickitem">besi &amp; baja</a>
                <a href="../home/cart.php" class="errorquickitem">keranjang</a>
                <a href="../home/about.php" class="errorquickitem">tentang kami</a>
            </div>
        </div>
    </div>
</div>

<?php
require __DIR__ . '/../home/partials/footer.php';
?>
