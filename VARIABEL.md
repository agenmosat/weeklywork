# Referensi Variabel PHP & Skema SQL (weeklywork, branch kerja)

Fungsi file ini: biar gampang nambah fitur karena tahu semua nama variable PHP dan struktur tabel SQL.

## 1. Skema database `toko`

### Tabel `products`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | int, AUTO_INCREMENT, PRIMARY KEY | tidak diisi manual |
| `name` | varchar(100), NOT NULL | nama produk |
| `price` | decimal(10,2), NOT NULL | harga |
| `stock` | int, NOT NULL | stok |
| `category` | varchar(50), NOT NULL | kategori |
| `description` | text, NOT NULL | deskripsi |

### Tabel `supplier`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_supplier` | int, AUTO_INCREMENT, PRIMARY KEY | tidak diisi manual |
| `nama_supplier` | varchar(50), NOT NULL | wajib, max 50 |
| `kota` | varchar(30), NULL | opsional, max 30 |

### Tabel `kasir`
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_kasir` | int, AUTO_INCREMENT, PRIMARY KEY | tidak diisi manual |
| `nama_kasir` | varchar(50), NOT NULL | wajib, max 50 |
| `status` | enum('aktif','nonaktif'), default 'aktif' | |
| `tanggal_keluar` | date, NULL | diisi saat nonaktif |

Config DB: `config/database.php` (lokal: 127.0.0.1, root, DB `toko`). Produksi: `db.php` dibuat oleh GitHub Actions dari secrets.

## 2. Semua variabel PHP di repository

### Superglobal
`$_GET` (input link), `$_POST` (input form), `$_SESSION` (login/cart), `$_SERVER` (REQUEST_METHOD)

### items.php
`$koneksi` — baca tabel products, hasil jadi:
- `$products` — array produk (id, name, price, stock, category, image, desc), key = id
- `$images` — peta id -> gambar
- `$row`, `$result`, `$id` — pemroses hasil query

### index.php (login)
- `$token` — input token form
- `$message` — pesan error login

### dashboard.php (toko)
- `$cartcount` — jumlah item di cart
- `$filtered` — produk setelah filter
- `$searchquery` (`$_GET['q']`), `$category` (`$_GET['cat']`), `$sort` (`$_GET['sort']`)
- `$temp`, `$prices`, `$names` — helper filter/sort
- `$catparam` — query string kategori
- `$p`, `$id`, `$qty` — loop tampilan

### product.php (detail produk)
- `$cartcount`, `$id` (`$_GET['id']`), `$product`, `$products`, `$qty`

### cart.php
- `$cartcount`, `$cartitems`, `$p`
- `$itemid`, `$qty` (`$_GET['add']`), `$addid`, `$newqty`
- `$removeid`, `$qid`, `$cid`, `$c` (`$_SESSION['cart']`)
- `$itemtotal`, `$subtotal`

### checkout.php
- `$cartcount`, `$checkoutitems`, `$directid`, `$directqty`
- `$ci`, `$cid`, `$qty`, `$p`
- `$itemtotal`, `$subtotal`, `$shipping`, `$total`

### payment.php
- `$cartcount`, `$name`, `$address`, `$city`
- `$qty`, `$shipping`, `$subtotal`, `$total`

### invoice.php
- `$ordernumber`, `$name`, `$address`, `$city`
- `$method`, `$ispaid`
- `$qty`, `$shipping`, `$subtotal`, `$total`

### crud/guard.php
- `$_SESSION` saja

### crud/product/product.php
- `$cari` (`$_GET['cari']`), `$result`, `$stmt`, `$koneksi`
- `$p` — row produk
- `$pesan` — pesan status dari `$_GET['status']`

### crud/product/addproduct.php
- `$error`, `$name`, `$price`, `$stock`, `$category`, `$description`
- `$stmt`, `$koneksi`, `$ok`

### crud/product/editproduct.php
- `$id` (`$_GET['id']` / `$_POST['id']`)
- `$error`, `$name`, `$price`, `$stock`, `$category`, `$description`
- `$row` — data lama, `$stmt`, `$koneksi`

### crud/product/delproduct.php
- `$id` (`$_POST['id']`), `$stmt`, `$koneksi`

### crud/supplier/supplier.php
- `$cari`, `$result`, `$stmt`, `$koneksi`
- `$s` — row supplier, `$pesan`

### crud/supplier/addsupplier.php
- `$error`, `$nama_supplier`, `$kota`, `$stmt`, `$koneksi`

### crud/supplier/editsupplier.php
- `$id` (`$_GET['id_supplier']` / `$_POST['id_supplier']`)
- `$error`, `$nama_supplier`, `$kota`, `$row`, `$stmt`, `$koneksi`

### crud/supplier/delsupplier.php
- `$id` (`$_POST['id_supplier']`), `$stmt`, `$koneksi`

### crud/cashier/cashier.php
- `$cari`, `$result`, `$stmt`, `$koneksi`
- `$k` — row kasir, `$pesan`

### crud/cashier/newcashier.php
- `$error`, `$nama_kasir`, `$status`, `$stmt`, `$koneksi`

### crud/cashier/editcashier.php
- `$id` (`$_GET['id_kasir']` / `$_POST['id_kasir']`)
- `$error`, `$nama_kasir`, `$status`, `$tanggal_keluar`
- `$tgl` — tanggal hari ini, `$row`, `$stmt`, `$koneksi`

### crud/cashier/offcashier.php
- `$id` (`$_POST['id_kasir']`), `$tgl`, `$stmt`, `$koneksi`

## 3. Peta input form -> variable -> kolom SQL

| Form input (name) | Variable PHP | Kolom SQL | File |
|---|---|---|---|
| `name` | `$name` | `products.name` | add/edit product |
| `price` | `$price` | `products.price` | add/edit product |
| `stock` | `$stock` | `products.stock` | add/edit product |
| `category` | `$category` | `products.category` | add/edit product |
| `description` | `$description` | `products.description` | add/edit product |
| `id` | `$id` | `products.id` | edit/del product |
| `nama_supplier` | `$nama_supplier` | `supplier.nama_supplier` | add/edit supplier |
| `kota` | `$kota` | `supplier.kota` | add/edit supplier |
| `id_supplier` | `$id` | `supplier.id_supplier` | edit/del supplier |
| `nama_kasir` | `$nama_kasir` | `kasir.nama_kasir` | new/edit cashier |
| `status` | `$status` | `kasir.status` | new/edit cashier |
| `tanggal_keluar` | `$tanggal_keluar` | `kasir.tanggal_keluar` | edit cashier |
| `id_kasir` | `$id` | `kasir.id_kasir` | edit/off cashier |
| `cari` | `$cari` | (filter LIKE, bukan kolom) | list CRUD |
| `q` | `$searchquery` | (filter array, bukan SQL) | dashboard |
| `token` | `$token` | (validasi login) | index |
| `add` | `$itemid` | (cart session) | cart |
| `remove` | `$removeid` | (cart session) | cart |

## 4. Resep nambah fitur baru

1. Tabel baru/kolom baru? tulis SQL + jalankan, lalu catat di bagian 1.
2. Copy file yang paling mirip, ganti nama tabel, kolom, dan parameter id.
3. Input form: `name="..."` -> `$_POST['...']` -> `bind_param('...', $var, ...)`, urutan tipe = urutan `?`.
4. SELECT: `mysqli_query` (tanpa input) atau prepared + `mysqli_stmt_get_result` (dengan input).
5. Ubah data: POST + prepared + redirect `header('Location: ...?status=...'); exit;`
6. Output: `htmlspecialchars(...)`.
