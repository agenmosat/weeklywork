<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/items.php';

$pagetitle = "layanan pelanggan - fivefonders.net";
require __DIR__ . '/../partials/header.php';
?>

    <?php require __DIR__ . '/../partials/topbar.php'; ?>

    <div class="layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <div class="content">
            <div class="heading">layanan pelanggan</div>

            <div class="servingcard" id="bantuan">
                <div class="servingtitle">pusat bantuan</div>
                <div class="servingtext">Temukan jawaban atas pertanyaan seputar pembelian bahan bangunan, penyewaan alat berat, dan sistem transaksi di fivefonders.net.</div>

                <div class="faqitem">
                    <div class="faqquest">Bagaimana cara memesan semen atau bata dalam jumlah besar?</div>
                    <div class="faqans">Anda dapat memilih produk dari katalog dan menentukan jumlah sak atau buah yang dibutuhkan. Sistem kami langsung menghitung harga total dan opsi pengiriman armada toko.</div>
                </div>

                <div class="faqitem">
                    <div class="faqquest">Apakah ada jaminan keamanan transaksi (rekening bersama)?</div>
                    <div class="faqans">Ya, transaksi di fivefonders.net dilindungi oleh sistem escrow (rekening bersama). Dana Anda ditahan secara aman dan baru diteruskan ke penjual setelah material tiba dan Anda konfirmasi.</div>
                </div>

                <div class="faqitem">
                    <div class="faqquest">Bagaimana alur penyewaan alat berat?</div>
                    <div class="faqans">Pilih alat berat yang diinginkan di kategori sewa (excavator, bulldozer, compactor, dll), lakukan checkout, dan tim kami akan menghubungi Anda untuk konfirmasi jadwal mobilisasi serta operator di lokasi proyek.</div>
                </div>
            </div>

            <div class="servingcard" id="pemesanan">
                <div class="servingtitle">cara pemesanan</div>
                <div class="servingtext">Belanja material proyek dan sewa alat berat di fivefonders.net sangat mudah dengan 4 langkah berikut:</div>

                <ul class="servinglist">
                    <li class="servingitem"><strong>1. Pilih Material atau Alat</strong>: Telusuri katalog produk sesuai kategori yang Anda butuhkan (semen, bata, besi, pasir, atau alat berat).</li>
                    <li class="servingitem"><strong>2. Masukkan ke Keranjang</strong>: Klik tombol beli untuk memasukkan produk dan sesuaikan jumlah kebutuhan material proyek Anda.</li>
                    <li class="servingitem"><strong>3. Isi Alamat &amp; Checkout</strong>: Masukkan nama pemesan, nomor telepon, dan titik pengantaran lokasi proyek Anda secara akurat.</li>
                    <li class="servingitem"><strong>4. Pembayaran &amp; Pengiriman</strong>: Pilih metode pembayaran bank transfer atau qris. Armada toko kami langsung berangkat mengantar ke titik proyek Anda.</li>
                </ul>
            </div>

            <div class="servingcard" id="pengiriman">
                <div class="servingtitle">pengiriman armada</div>
                <div class="servingtext">Kami mengoperasikan armada mandiri untuk memastikan pasokan material sampai tepat waktu di lapangan tanpa keterlambatan.</div>

                <ul class="servinglist">
                    <li class="servingitem"><strong>Truk Dump Toko</strong>: Khusus angkut pasir, batu kali, split, sirtu, dan bata dalam kapasitas sedang hingga besar (5-8 kubik).</li>
                    <li class="servingitem"><strong>Pick-up Toko</strong>: Pengiriman cepat material ringkas seperti semen, cat, paku, dan aksesoris bangunan.</li>
                    <li class="servingitem"><strong>Same Day Delivery</strong>: Pesanan yang dikonfirmasi sebelum jam 14.00 WIB dikirim pada hari yang sama untuk wilayah Surabaya, Sidoarjo, dan Gresik.</li>
                    <li class="servingitem"><strong>Ambil Sendiri</strong>: Anda juga dapat mengambil langsung material di gudang toko mitra terdekat kami.</li>
                </ul>
            </div>

            <div class="servingcard" id="syarat">
                <div class="servingtitle">syarat dan ketentuan</div>
                <div class="servingtext">Ketentuan belanja dan bertransaksi di platform fivefonders.net:</div>

                <ul class="servinglist">
                    <li class="servingitem">Semua harga yang tertera di katalog sudah termasuk pajak dan berlaku sesuai stok distributor lokal.</li>
                    <li class="servingitem">Pembeli wajib memeriksa kondisi fisik dan kuantitas material saat armada toko tiba sebelum menandatangani surat jalan.</li>
                    <li class="servingitem">Retur material yang cacat atau tidak sesuai spesifikasi dapat diajukan dalam waktu 1x24 jam setelah penerimaan barang.</li>
                    <li class="servingitem">Pembatalan pesanan sewa alat berat minimal diajukan 2 hari sebelum tanggal mobilisasi unit ke lokasi proyek.</li>
                </ul>
            </div>

            <div class="servingcard" id="kontak">
                <div class="servingtitle">hubungi kami</div>
                <div class="servingtext">Tim dukungan pelanggan kami siap membantu kelancaran pengadaan material konstruksi dan kebutuhan alat berat Anda.</div>

                <ul class="servinglist">
                    <li class="servingitem"><strong>Layanan Email</strong>: help@fivefonders.net</li>
                    <li class="servingitem"><strong>Jam Operasional</strong>: Senin - Sabtu: 08.00 - 17.00 WIB (Minggu &amp; Hari Libur Nasional tutup)</li>
                </ul>
            </div>
        </div>
    </div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
