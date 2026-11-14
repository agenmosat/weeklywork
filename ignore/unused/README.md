# KasirQu

Contoh aplikasi kasir sederhana berbasis PHP. Halaman utama memakai data
contoh; materi form ada di `latihan/form/`, sedangkan konfigurasi koneksi
database ada di `config/`.

## Menjalankan

Tempatkan folder proyek di dalam document root server PHP (misalnya
`htdocs/triofonderwork`), lalu buka `http://localhost/triofonderwork/`.
Header menghitung awalan URL proyek dari lokasi folder, sehingga tautan
dan CSS tetap berfungsi saat nama atau posisi folder proyek berubah.

## Struktur

```text
triofonderwork/
├── assets/css/
│   ├── style.css          # stylesheet utama
│   └── crud.css           # stylesheet tambahan
├── config/
│   └── database.php       # koneksi MySQL dan pengaturan DB
├── includes/
│   ├── header.php         # head, navigasi, pembuka konten
│   └── footer.php         # penutup konten dan HTML
├── latihan/form/          # materi form dan contoh GET/POST
├── index.php              # dashboard
├── produk.php             # daftar produk
├── transaksi.php          # contoh hitung total transaksi
└── koneksi.php            # halaman pemeriksaan koneksi database
```

## Jalur halaman

- Dashboard: `index.php`
- Produk: `produk.php`
- Transaksi: `transaksi.php`
- Materi form: `latihan/form/form.php`
- Contoh dasar, GET, dan POST: file terkait di `latihan/form/`
- Pemeriksaan database: `koneksi.php`

Semua halaman aplikasi menggunakan partial `includes/header.php` dan
`includes/footer.php`. Halaman bertingkat memanggil partial melalui jalur
filesystem berbasis `__DIR__`; tautan browser dibentuk dari awalan URL proyek.

## Database

Sesuaikan konstanta di `config/database.php` dengan database lokal. File itu
membuat `$koneksi`; halaman `koneksi.php` menampilkan status koneksi. Jangan
menyimpan kredensial produksi di repository publik. 

陰 : The Negative, that you've granted

陽 : The Positive, that you've taken

衡 : The Equinox, Everything from everywhere, even from nothing

無 : And you, now having it all, bring yourself close to The ZERO.