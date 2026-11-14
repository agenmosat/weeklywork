# INI YANG HARUS LU KERJAIN!

1. buat codenya bergantung sama database (pening nih gw setupnya 🗿)
2. beresin yang ga berantakan (rapihin kodenya)
3. bantu fix kan issuenya
4. file file baru sebagai berikut
 create mode 100644 assets/transaksi.js
 create mode 100644 checklist.php
 create mode 100644 pembelian.php
 create mode 100644 pembelian_detail.php
 create mode 100644 pembelian_tambah.php
 create mode 100644 penjualan.php
 create mode 100644 penjualan_detail.php
 create mode 100644 penjualan_tambah.php
 create mode 100644 transaksi_helper.php

001. darkmode masih kasar (benarkan) status : fixed
002. barang-barang irrelevant (hilangkan) status : fixed
003. kategori-kategori irrelevant (hilangkan) status : fixed
004. gambar-gambar masih kosong (tambahkan) status : fixed
005. dasbor admin belum ada/berfungsi (tambahkan) status : fixed
006. data produk masih hardcoded (koneksikan ke database) status : fixed
007. validasi stok di server kurang ketat (validasi ulang sisi backend) status : fixed
008. kode UI redundan/berulang (gunakan modular include/require) status : fixed
009. UI belum responsif untuk versi mobile (tambahkan aturan media query) status : fixed
010. sistem pembayaran escrow palsu (integrasikan gateway API) status : fixed
011. hotlinking aset gambar eksternal (gunakan aset lokal) status : fixed
012. duplikasi script JavaScript ekstrem (pisahkan ke file .js eksternal) status : fixed
013. penanganan error 404 produk gak valid (berikan halaman not found) status : fixed
014. pembersihan keranjang terlalu prematur (perbaiki alur pemusnahan sesi) status : fixed
015. stok produk tidak berkurang otomatis setelah checkout (sinkronkan pemotongan stok pada database) status : fixed
016. riwayat transaksi pembelian tidak tersimpan (buat tabel pesanan dan simpan data transaksi) status : fixed
017. manajemen gambar belum tersedia pada form CRUD produk (tambahkan input nama/unggah file gambar) status : fixed
018. pencarian dan penyaringan kategori katalog masih memproses array memori (optimalkan ke query database) status : fixed
019. pembagian halaman atau pagination belum diterapkan (implementasikan limit dan offset query) status : fixed
020. komponen pemilih tema dinonaktifkan pada halaman dashboard utama (aktifkan kembali dropdown tema) status : fixed
021. inkonsistensi label dan tautan navigasi antar halaman (seragamkan istilah navigasi store dan dashboard) status : fixed
022. sistem autentikasi kasir belum terhubung dengan data tabel kasir (implementasikan login kasir mandiri) status : fixed
023. ketiadaan dialog konfirmasi sebelum aksi hapus atau pengosongan keranjang (tambahkan konfirmasi aksi pengguna) status : fixed
024. fitur cetak atau ekspor struk invoice belum tersedia (tambahkan fungsi cetak atau simpan dokumen faktur) status : fixed
025. total harga pada form checkout rentan dimanipulasi dari client (hitung ulang total belanja di sisi server) status : fixed
026. tidak adanya batas kedaluwarsa sesi pengguna (tambahkan mekanisme session timeout otomatis) status : fixed

-- new issues --

# dinda
027. duplikasi boilerplate header html pada setiap halaman (pisahkan ke template modular header.php) status : pending
028. duplikasi penutup tag dokumen dan script footer html (pisahkan ke template modular footer.php) status : pending
029. duplikasi markup bar navigasi atas topbar pada halaman admin dan modul crud (gunakan include topbar.php) status : pending
030. duplikasi markup menu sidebar admin pada seluruh halaman modul crud (buat template modular sidebar_admin.php) status : pending

# alfredo
031. kueri penyemaian data database menggunakan union all select dan subquery bertingkat yang rumit (sederhanakan dengan perintah insert into values standar) status : pending
032. kueri penghapusan data duplikat menggunakan multi-table self join yang sulit dipahami pemula (sederhanakan mekanisme pembersihan data tabel) status : pending
033. manipulasi skema tabel melalui kueri alter table dan show columns saat proses form crud produk (pindahkan definisi kolom gambar ke berkas skema sql) status : pending
034. eksekusi kueri ddl create table secara dinamis di dalam skrip alur transaksi invoice (pindahkan inisialisasi tabel pesanan ke skrip migrasi database) status : pending

# gery
035. redundansi logika kalkulasi subtotal dan validasi item pada checkout, payment, dan invoice (satukan ke fungsi helper kalkulasi pesanan) status : pending
036. duplikasi penanganan aksi logout di berbagai file halaman utama (seragamkan pengalihan sesi keluar melalui berkas logout.php) status : pending
037. pemanggilan berkas items.php berulang dan pemuatan query database redundan pada katalog dashboard (optimalkan pemanggilan berkas dan hilangkan query ganda) status : pending
038. pemanggilan fungsi session_set_cookie_params setelah session_start aktif (pindahkan pengaturan parameter cookie sebelum pemanggilan session_start) status : pending

# nell
039. implementasi kueri berparameter dinamis menggunakan variadic unpacking dan penggabungan tipe yang kompleks (sederhanakan alur prepared statement dasar untuk pemula) status : pending
040. dualisme sumber data gambar antara array asosiatif hardcoded dan kolom tabel produk (jadikan kolom database sebagai satu-satunya sumber data gambar) status : pending
041. mekanisme pengalihan batas waktu sesi menggunakan manipulasi string script_name yang rentan bermasalah (standarisasi routing pengalihan sesi menggunakan base url atau path absolut) status : pending
042. ketiadaan implementasi modul proteksi token csrf pada seluruh formulir transaksi dan crud (terapkan token csrf pada setiap form post aplikasi) status : pending

#fahri
043. duplikasi fungsi dialog konfirmasi tindakan pada script javascript utama (sederhanakan menjadi satu fungsi konfirmasi generik) status : pending
044. duplikasi elemen pemilih tema dropdown pada berbagai template dokumen (jadikan komponen tema terintegrasi dalam bar navigasi) status : pending
045. variabel attempts_key dan now tidak terdefinisi pada logika pembatasan login (perbaiki atau hapus blok kode rate limit yang rusak) status : pending
046. penggunaan fungsi sql concat di dalam klausa pencarian like pada modul crud (sederhanakan penggabungan wildcard pencarian di sisi php) status : pending
047. struktur basis data transaksi belum ternormalisasi karena ketiadaan tabel rincian transaksi (tambahkan tabel relasi penjualan_detail dan pembelian_detail) status : pending

kali ini itu aja kayaknya :v

EXTRA (dari pm)
ini list yang harus ditambahkan
1. ekripsi url
2. tba
