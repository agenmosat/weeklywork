# DAFTAR TUGAS MENERAPKAN BOOTSTRAP

Dokumen ini memuat daftar tugas penerapan Bootstrap 5 pada proyek aplikasi kasir agar antarmuka rapi, responsif, dan mudah digunakan.

---

## 1. Setup & Template Modular

001. Pasang pustaka Bootstrap 5 (CSS & JS) di file partials (header dan footer) status : done
002. Rapikan template modular partials/header.php dan partials/footer.php status : done
003. Terapkan bar navigasi partials/topbar.php menggunakan navbar Bootstrap status : pending
004. Terapkan menu navigasi partials/sidebar.php menggunakan komponen nav Bootstrap status : pending

---

## 2. Halaman Login

005. Ubah tampilan halaman login (index.php) menggunakan card Bootstrap di tengah layar status : pending
006. Terapkan form-label, form-control, dan btn Bootstrap pada formulir login status : pending
007. Terapkan alert Bootstrap untuk pesan error dan notifikasi sesi habis status : pending

### Dinda
027. Duplikasi kerangka awal dokumen HTML pada setiap halaman (pisahkan ke template modular header.php) status : pending
028. Duplikasi penutup dokumen HTML dan script pada setiap halaman (pisahkan ke template modular footer.php) status : pending
029. Duplikasi markup bar navigasi atas pada antarmuka admin dan modul CRUD (terapkan pemanggilan terpusat melalui partials/topbar.php) status : done
030. Duplikasi markup navigasi menu bilah sisi pada seluruh modul administrasi (terapkan template modular bilah sisi admin) status : pending

### Alfredo
031. Kueri penyemaian data awal basis data menggunakan UNION ALL dan subkueri bertingkat (sederhanakan menggunakan perintah INSERT INTO VALUES standar) status : done
032. Kueri penghapusan data duplikat menggunakan penggabungan multi-tabel yang rumit (sederhanakan mekanisme pembersihan data tabel) status : done
033. Manipulasi struktur tabel menggunakan perintah DDL ALTER TABLE pada saat pemrosesan formulir (pindahkan definisi kolom ke skema basis data utama) status : pending
034. Eksekusi pembuatan tabel dinamis pada alur pembuatan faktur pembayaran (pindahkan inisialisasi tabel pesanan ke skrip migrasi basis data) status : pending

### Gery
035. Redundansi logika kalkulasi subtotal dan validasi item belanja pada modul transaksi (satukan ke fungsi helper kalkulasi terpusat) status : done
036. Duplikasi penanganan proses keluar sistem di berbagai dokumen (seragamkan pengalihan keluar melalui berkas logout.php terpusat) status : done
037. Pemanggilan berkas pendukung data produk berulang dan pemuatan kueri ganda pada katalog (optimalkan alur pemanggilan berkas dan hilangkan kueri redundan) status : done
038. Pengaturan parameter kuki sesi dipanggil setelah inisialisasi sesi aktif (pindahkan pemanggilan session_set_cookie_params sebelum session_start) status : done

### Nell
039. Kueri berparameter dinamis menggunakan variadic unpacking dan manipulasi tipe data kompleks (sederhanakan alur prepared statement standar yang terstruktur) status : done
040. Dualisme sumber data gambar antara array asosiatif dan kolom tabel basis data (tetapkan kolom tabel produk sebagai satu-satunya sumber data gambar) status : done
041. Mekanisme pengalihan batas waktu sesi menggunakan pemotongan string SCRIPT_NAME yang rentan galat (standarisasi pengalihan sesi menggunakan jalur absolut atau path terstruktur) status : done
042. Ketiadaan proteksi token CSRF pada formulir transaksi dan manipulasi data (terapkan verifikasi token CSRF pada seluruh formulir berstatus POST) status : done

### Fahri
043. Duplikasi fungsi dialog konfirmasi tindakan pengguna pada berkas JavaScript (sederhanakan menjadi fungsi konfirmasi generik terpadu) status : done
044. Duplikasi komponen pemilih tema antarmuka pada berbagai dokumen (integrasikan pemilih tema ke dalam bar navigasi utama) status : done
045. Variabel penampung batas percobaan login tidak terdefinisi pada alur login (perbaiki mekanisme rate limiting atau bersihkan blok kode bermasalah) status : pending
046. Penggunaan fungsi CONCAT SQL pada penyaringan pencarian tabel modul CRUD (sederhanakan penggabungan wildcard pencarian di sisi PHP) status : done
047. Struktur basis data transaksi belum memenuhi kaidah normalisasi (buat tabel relasi penjualan_detail dan pembelian_detail) status : done

---

## 3. Halaman Toko & Kasir (Home)

008. Terapkan grid card Bootstrap untuk katalog produk di home/dashboard.php status : pending
009. Terapkan pagination Bootstrap untuk pembagian halaman produk status : pending
010. Terapkan layout card dan tombol beli pada halaman detail produk (home/product.php) status : pending
011. Terapkan tabel Bootstrap (table table-striped) pada keranjang belanja (home/cart.php) status : pending
012. Terapkan form checkout dan rincian belanja di home/checkout.php status : pending
013. Terapkan card dan tombol pembayaran di home/payment.php status : pending
014. Terapkan tampilan struk faktur pembayaran yang rapi di home/invoice.php status : pending

---

## 4. Halaman Dashboard Admin

015. Terapkan layout grid dan card ringkasan statistik di admin/admin.php status : pending
016. Pastikan tampilan dashboard admin responsif di layar mobile status : pending

---

## 5. Modul CRUD Produk

017. Terapkan tabel Bootstrap dan badge stok pada daftar produk (admin/product/product.php) status : pending
018. Terapkan form control Bootstrap pada form tambah produk (admin/product/addproduct.php) status : pending
019. Terapkan form control Bootstrap pada form edit produk (admin/product/editproduct.php) status : pending
020. Terapkan modal atau alert konfirmasi pada hapus produk (admin/product/delproduct.php) status : pending

---

## 6. Modul CRUD Kasir & Supplier

021. Terapkan tabel Bootstrap pada daftar kasir (admin/cashier/cashier.php) status : pending
022. Terapkan form Bootstrap pada tambah dan edit kasir (admin/cashier/) status : pending
023. Terapkan tabel Bootstrap pada daftar supplier (admin/supplier/supplier.php) status : pending
024. Terapkan form Bootstrap pada tambah dan edit supplier (admin/supplier/) status : pending

---

## 7. Modul Transaksi & Laporan

025. Terapkan tabel Bootstrap pada riwayat penjualan (admin/penjualan/penjualan.php) status : pending
026. Terapkan form Bootstrap pada tambah penjualan (admin/penjualan/penjualan_tambah.php) status : pending
027. Terapkan card dan tabel pada detail penjualan (admin/penjualan/penjualan_detail.php) status : pending
028. Terapkan tabel dan form Bootstrap pada modul pembelian (admin/pembelian/) status : pending

---

## 8. Komponen Tambahan & Perapihan

029. Ganti pesan notifikasi dan peringatan manual menggunakan alert Bootstrap status : pending
030. Bersihkan class CSS lama yang sudah digantikan oleh Bootstrap utility classes status : pending
