# DAFTAR ISU DAN TUGAS PENGEMBANGAN PROYEK

Dokumen ini memuat seluruh rekapitulasi tugas, perbaikan bug, penyelarasan kurikulum Modul Ajar Pemrograman Web (KasirQu / KasirSMK), serta instruksi tambahan yang harus diselesaikan pada proyek aplikasi kasir.

---

## 1. Isu Awal & Perbaikan Dasar Sistem 

001. Penyesuaian transisi dan kontras tampilan mode gelap (perbaiki styling CSS dark mode agar lebih halus dan konsisten) status : fixed
002. Keberadaan entitas produk yang tidak relevan dengan katalog toko (bersihkan dan hapus data barang yang tidak sesuai dari sistem) status : fixed
003. Keberadaan kategori produk yang tidak relevan (singkirkan kategori yang tidak sesuai dari basis data dan navigasi) status : fixed
004. Ketiadaan aset gambar visual pada sebagian besar data produk (lengkapi aset gambar lokal untuk seluruh produk katalog) status : fixed
005. Dasbor administrasi belum tersedia atau belum berfungsi (bangun antarmuka dasbor admin untuk manajemen toko) status : fixed
006. Data katalog produk masih tersimpan secara statis di dalam kode (hubungkan pemuatan data produk langsung dari basis data) status : fixed
007. Validasi ketersediaan stok produk pada sisi server kurang ketat (perketat validasi stok di sisi backend sebelum memproses pesanan) status : fixed
008. Duplikasi struktur kode antarmuka pengguna pada berbagai halaman (terapkan modularisasi kode antarmuka menggunakan include dan require) status : fixed
009. Antarmuka pengguna belum responsif terhadap perangkat mobile (tambahkan aturan CSS media query untuk tata letak layar kecil) status : fixed
010. Mekanisme pembayaran escrow masih bersifat simulasi statis (integrasikan modul API gateway pembayaran mandiri) status : fixed
011. Penggunaan tautan langsung ke aset gambar eksternal (alihkan seluruh tautan gambar ke penyimpanan aset lokal) status : fixed
012. Duplikasi kode JavaScript yang tersebar di banyak dokumen (ekstraksi dan satukan script logika ke dalam berkas JavaScript eksternal) status : fixed
013. Penanganan galat 404 ketika mengakses ID produk yang tidak valid belum tersedia (sediakan tampilan halaman not found yang informatif) status : fixed
014. Pembersihan data sesi keranjang belanja terjadi terlalu prematur (perbaiki siklus hidup sesi agar keranjang tidak terhapus sebelum transaksi tuntas) status : fixed
015. Stok produk pada basis data tidak berkurang setelah proses checkout selesai (sinkronkan pemotongan stok otomatis pada saat pesanan berhasil disimpan) status : fixed
016. Riwayat transaksi pembelian pelanggan belum tersimpan secara permanen (buat tabel pesanan dan simpan data setiap transaksi pembayaran) status : fixed
017. Antarmuka formulir CRUD produk belum mendukung pengelolaan berkas gambar (tambahkan input nama atau unggah berkas gambar pada form produk) status : fixed
018. Mekanisme pencarian dan penyaringan kategori masih memproses array di memori (optimalkan logika pencarian langsung ke kueri basis data) status : fixed
019. Pembagian halaman data katalog belum diimplementasikan (terapkan mekanisme pagination menggunakan klausa LIMIT dan OFFSET pada kueri) status : fixed
020. Komponen dropdown pemilih tema dinonaktifkan pada dasbor toko utama (aktifkan kembali interaktivitas pengubah tema) status : fixed
021. Inkonsistensi penamaan label dan tautan navigasi antarhalaman (seragamkan istilah dan target tautan navigasi aplikasi) status : fixed
022. Autentikasi kasir belum terintegrasi dengan tabel kasir pada basis data (terapkan verifikasi login kasir mandiri melalui basis data) status : fixed
023. Ketiadaan dialog konfirmasi sebelum aksi penghapusan atau pengosongan keranjang (sediakan modal atau dialog konfirmasi tindakan pengguna) status : fixed
024. Fitur pencetakan struk faktur pembayaran belum tersedia (tambahkan fungsi cetak dokumen invoice menggunakan stylesheet cetak) status : fixed
025. Perhitungan total harga pada formulir checkout rentan dimanipulasi dari sisi klien (kalkulasi ulang total belanja secara mutlak di sisi server) status : fixed
026. Ketiadaan batas masa aktif sesi pengguna yang masuk ke sistem (terapkan mekanisme session timeout otomatis setelah periode inaktivitas tertentu) status : fixed

---

## 2. Isu Pembagian Tim 

### Dinda
027. Duplikasi kerangka awal dokumen HTML pada setiap halaman (pisahkan ke template modular header.php) status : done
028. Duplikasi penutup dokumen HTML dan script pada setiap halaman (pisahkan ke template modular footer.php) status : done
029. Duplikasi markup bar navigasi atas pada antarmuka admin dan modul CRUD (terapkan pemanggilan terpusat melalui partials/topbar.php) status : done
030. Duplikasi markup navigasi menu bilah sisi pada seluruh modul administrasi (terapkan template modular bilah sisi admin) status : done

### Alfredo
031. Kueri penyemaian data awal basis data menggunakan UNION ALL dan subkueri bertingkat (sederhanakan menggunakan perintah INSERT INTO VALUES standar) status : done
032. Kueri penghapusan data duplikat menggunakan penggabungan multi-tabel yang rumit (sederhanakan mekanisme pembersihan data tabel) status : done
033. Manipulasi struktur tabel menggunakan perintah DDL ALTER TABLE pada saat pemrosesan formulir (pindahkan definisi kolom ke skema basis data utama) status : done
034. Eksekusi pembuatan tabel dinamis pada alur pembuatan faktur pembayaran (pindahkan inisialisasi tabel pesanan ke skrip migrasi basis data) status : done

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
045. Variabel penampung batas percobaan login tidak terdefinisi pada alur login (perbaiki mekanisme rate limiting atau bersihkan blok kode bermasalah) status : done
046. Penggunaan fungsi CONCAT SQL pada penyaringan pencarian tabel modul CRUD (sederhanakan penggabungan wildcard pencarian di sisi PHP) status : done
047. Struktur basis data transaksi belum memenuhi kaidah normalisasi (buat tabel relasi penjualan_detail dan pembelian_detail) status : done

---

## 3. Tugas Modul Lanjutan Berdasarkan Modul Ajar

### Transaksi & Kartu Stok
048. Ketiadaan pencatatan mutasi kartu stok barang secara kronologis (bangun tabel stok untuk mencatat setiap peristiwa barang masuk dan keluar beserta saldo awal dan saldo akhir) status : done
049. Formulir transaksi pembelian dan penjualan hanya mendukung penambahan satu jenis barang per nota (kembangkan formulir multi-baris dinamis berbasis JavaScript dan penanganan array input) status : done
050. Ketiadaan penanganan transaksi basis data atomik pada penyimpanan nota (terapkan blok transaksi mysqli_begin_transaction, commit, dan rollback pada modul transaksi) status : done
051. Format penomoran transaksi nota belum sekuensial dan terstandarisasi (implementasikan generator nomor transaksi otomatis berurutan dengan format PB-XXXX dan PJ-XXXX) status : done
052. Ketiadaan prosedur pembatalan transaksi nota yang aman (sediakan fitur pembatalan nota dengan validasi ketersediaan saldo dan pencatatan mutasi stok pembalik) status : done

### Pengamanan Integritas Data Master
053. Penghapusan data produk tidak memvalidasi riwayat penggunaan pada nota transaksi (pasang validasi pengecekan relasi detail transaksi sebelum eksekusi penghapusan produk) status : done
054. Penghapusan data pemasok (supplier) tidak memvalidasi riwayat transaksi pembelian (pasang validasi pengecekan relasi nota pembelian sebelum eksekusi penghapusan supplier) status : done

### Modul Laporan & Rekapitulasi Bisnis 
055. Ketiadaan modul laporan rekapitulasi penjualan per periode (buat halaman laporanpenjualan.php dengan filter rentang tanggal, agregasi total, dan rincian transaksi) status : done
056. Ketiadaan modul laporan rekapitulasi pembelian per periode (buat halaman laporanpembelian.php dengan filter tanggal dan rekapitulasi biaya belanja ke pemasok) status : done
057. Ketiadaan modul laporan pemantauan sisa stok inventaris (buat halaman laporanstok.php yang menampilkan saldo terkini dan filter peringatan stok minimum) status : done
058. Ketiadaan modul laporan estimasi laba kotor toko (buat halaman laporanlaba.php yang mengkalkulasi selisih omzet penjualan terhadap harga modal beli produk) status : done
059. Ketiadaan fasilitas cetak dokumen fisik pada seluruh halaman laporan (tambahkan tombol cetak dengan integrasi window.print dan stylesheet khusus media cetak) status : done

### Manajemen Pengguna, Otorisasi, & Sesi
060. Sistem autentikasi pengguna belum terhubung dengan tabel data akun terpusat (implementasikan tabel pengguna dengan penyimpanan kata sandi terenkripsi hash bcrypt) status : done
061. Pembatasan hak akses berbasis peran (role) belum diterapkan secara menyeluruh di tingkat peladen (terapkan modul otorisasi peran admin dan kasir pada setiap halaman bisnis) status : done
062. Alur keluar sistem (logout) masih dapat dipicu melalui metode HTTP GET (perbarui tombol logout menjadi formulir metode POST dengan regenerasi ID sesi) status : done

---

## 4. Instruksi Tambahan Project Manager 

063. Keterpaparan kunci utama produk pada parameter URL publik (terapkan mekanisme penyamaran atau enkripsi URL parameter untuk menyembunyikan primary key produk) status : done
064. Ketiadaan alur verifikasi alamat surat elektronik pengguna (integrasikan mekanisme verifikasi email pada modul registrasi atau profil akun) status : pending
065. Pustaka antarmuka Bootstrap 5 lokal belum terpasang (sediakan aset Bootstrap CSS dan JS lokal di dalam direktori aset untuk mendukung stabilitas tata letak) status : pending
