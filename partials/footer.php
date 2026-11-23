<?php
$footerbase = '';
if (file_exists('assets/main.js')) {
    $footerbase = '';
} elseif (file_exists('../assets/main.js')) {
    $footerbase = '../';
} elseif (file_exists('../../assets/main.js')) {
    $footerbase = '../../';
}
?>
<footer class="footer">
    <div class="footerwrap">
        <div class="footergrid">
            <div class="footercol">
                <div class="footertitle">layanan pelanggan</div>
                <ul class="footerlist">
                    <li class="footeritem"><a href="<?= $footerbase ?>home/serving.php#bantuan" class="footerlink">pusat bantuan</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/serving.php#pemesanan" class="footerlink">cara pemesanan</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/serving.php#pengiriman" class="footerlink">pengiriman armada</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/serving.php#syarat" class="footerlink">syarat dan ketentuan</a></li>
                </ul>
            </div>
            <div class="footercol">
                <div class="footertitle">tentang fivefonders</div>
                <ul class="footerlist">
                    <li class="footeritem"><a href="<?= $footerbase ?>home/about.php" class="footerlink">tentang kami</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/about.php#tim" class="footerlink">tim pengembang</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/dashboard.php" class="footerlink">katalog produk</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/serving.php#kontak" class="footerlink">hubungi kami</a></li>
                </ul>
            </div>
            <div class="footercol">
                <div class="footertitle">kategori bahan</div>
                <ul class="footerlist">
                    <li class="footeritem"><a href="<?= $footerbase ?>home/dashboard.php?cat=Semen" class="footerlink">semen & mortar</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/dashboard.php?cat=Bata" class="footerlink">bata & batako</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/dashboard.php?cat=Besi+%26+Baja" class="footerlink">besi & baja</a></li>
                    <li class="footeritem"><a href="<?= $footerbase ?>home/dashboard.php?cat=Excavator" class="footerlink">sewa alat berat</a></li>
                </ul>
            </div>
            <div class="footercol">
                <div class="footertitle">pembayaran</div>
                <div class="footermethods">
                    <span class="footertag">bca</span>
                    <span class="footertag">mandiri</span>
                    <span class="footertag">bri</span>
                    <span class="footertag">bni</span>
                    <span class="footertag">qris</span>
                    <span class="footertag">cod</span>
                </div>
                <div class="footertitle" style="margin-top:14px">armada pengiriman</div>
                <div class="footermethods">
                    <span class="footertag">truk toko</span>
                    <span class="footertag">pick up</span>
                    <span class="footertag">ambil sendiri</span>
                </div>
            </div>
        </div>
        <div class="footerbottom">
            <div class="footercopy">&copy; 2026 fivefonders.net. seluruh hak cipta dilindungi. platform penyedia bahan bangunan & penyewaan alat berat terpercaya.</div>
        </div>
    </div>
</footer>
<script src="<?= $footerbase ?>assets/main.js"></script>
</body>
</html>