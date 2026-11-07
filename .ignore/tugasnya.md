# KasirSMK - Proyek Akhir P15-P16

Proyek CRUD Toko Maju Jaya memakai **PHP natif + MySQLi + Bootstrap 5 lokal**.
Database: **`db_penjualan`**.

## Buka di browser

```
http://localhost/smk4/kasirsmk/
```

Alternatif Laragon: `http://smk4.test/kasirsmk/`

## Struktur

```
kasirsmk/
├── partials/
│   ├── header.php          # Bootstrap navbar, CSS, pembuka container
│   └── footer.php          # penutup container, footer, JS Bootstrap
├── config/
│   └── database.php        # koneksi ke db_penjualan
├── assets/
│   ├── bootstrap/          # Bootstrap 5 lokal
│   ├── jquery/             # jQuery 3.7.1 lokal
│   ├── produk.js           # interaksi form produk
│   ├── presentasi.css      # tampilan slide presentasi
│   ├── presentasi.js       # navigasi slide presentasi
│   └── style.css
├── index.php               # dashboard
├── panduan.php             # panduan pembuatan + brief tugas
├── presentasi.php          # presentasi mengajar P15-P16
├── produk.php              # READ + pencarian
├── produk_tambah.php       # CREATE
├── produk_edit.php         # UPDATE
├── produk_hapus.php        # DELETE
├── kasir.php               # CONTOH: daftar kasir
├── kasir_tambah.php        # CONTOH: form tanpa tombol simpan
├── kasir_edit.php          # TUGAS: edit kasir
├── kasir_nonaktifkan.php   # TUGAS: soft delete kasir
├── supplier.php            # TUGAS: daftar supplier
├── supplier_tambah.php     # TUGAS: tambah supplier
├── supplier_edit.php       # TUGAS: edit supplier
├── supplier_hapus.php      # TUGAS: hapus supplier + cek pemakaian
└── README.md
```

## Cara belajar

1. Buka dashboard, lalu pilih **Mode Presentasi** untuk mengajar P15-P16.
2. Baca **Penjelasan Produk per Form** di `panduan.php` sebagai bahan diskusi.
3. Coba **Hasil Akhir Produk**: daftar, cari, tambah, edit, dan hapus.
4. Perhatikan jQuery di `assets/produk.js`: huruf besar, counter nama, laba,
   validasi, konfirmasi hapus, dan anti klik ganda.
5. Buka **Kasir**: daftar sudah tersedia dan `kasir_tambah.php` berisi contoh
   form tanpa tombol simpan.
6. Baca bagian **Tugas Kasir** dan **Tugas Supplier**, lalu lengkapi file tugas.
7. Uji semua fitur dengan data dari database `db_penjualan`.

## Aturan tugas

- Tambah, edit, dan hapus memakai **prepared statement**.
- Semua output memakai **`htmlspecialchars`**.
- Aksi yang mengubah data memakai **POST**.
- jQuery hanya membantu tampilan; validasi tetap dilakukan di PHP.
- Kasir tidak dihapus permanen, tetapi **dinonaktifkan**.
- Supplier tidak boleh dihapus jika sudah dipakai di tabel `pembelian`.
- Navbar menampilkan **Kasir**; tautan **Supplier** sengaja dikomentari sampai
  tugas supplier selesai.

## Tabel utama

| Tabel | Primary key | Keterangan |
|---|---|---|
| `produk` | `kode_produk` | Contoh CRUD lengkap |
| `kasir` | `id_kasir` | Tugas siswa |
| `supplier` | `id_supplier` | Tugas siswa |