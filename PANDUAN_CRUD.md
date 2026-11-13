# Panduan CRUD PHP (repo weeklywork, branch kerja)

## 1. Alur dasar

1. UI (link/form) kirim request ke file PHP.
2. File PHP: `require guard.php` (kalau admin), `require config/database.php` (kalau butuh DB).
3. Baca input: `$_GET['...']` dari link `?id=1`, `$_POST['...']` dari form `name="..."`.
4. Query DB, lalu tampilkan HTML atau `header('Location: ...')` + `exit`.

Cek nyambung: `grep -rn "namafile.php" .`

## 2. Bikin file tambah data (INSERT)

Contoh pola: `crud/supplier/addsupplier.php`

- Form: `method="post" action="addsupplier.php"`, setiap field punya `name="..."`.
- PHP atas file:
  - `if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... }`
  - Ambil: `$nama = trim($_POST['nama_supplier'] ?? '');`
  - Validasi server-side.
  - Siapkan query dengan placeholder `?` (jangan tempel string user ke SQL).
  - `mysqli_prepare` -> `mysqli_stmt_bind_param` -> `mysqli_stmt_execute`.
  - Sukses: `header('Location: supplier.php?status=tambah'); exit;`

```php
$stmt = mysqli_prepare($koneksi, 'INSERT INTO supplier (nama_supplier, kota) VALUES (?, ?)');
mysqli_stmt_bind_param($stmt, 'ss', $nama_supplier, $kota);
if (mysqli_stmt_execute($stmt)) {
    header('Location: supplier.php?status=tambah');
    exit;
}
```

Tipe bind: `s` string, `i` integer, `d` double. Urutan tipe & variabel = urutan `?`.

## 3. Query di PHP (mysqli)

Koneksi: `config/database.php` -> `$koneksi`. Semua file pakai `require_once __DIR__ . '/.../config/database.php';`

- Baca (tanpa input user): `mysqli_query` + loop `mysqli_fetch_assoc`.
```php
$result = mysqli_query($koneksi, 'SELECT id_supplier, nama_supplier FROM supplier ORDER BY id_supplier');
while ($row = mysqli_fetch_assoc($result)) {
    echo htmlspecialchars($row['nama_supplier']);
}
```
- Baca/tulis dengan input user: selalu prepared statement (`prepare`/`bind_param`/`execute`).
  - SELECT dengan input: `mysqli_stmt_get_result($stmt)` lalu `fetch_assoc`.
- Edit: GET `?id` -> SELECT isi form; POST -> UPDATE `... WHERE id = ?`.
- Hapus/off: POST + hidden id -> DELETE atau UPDATE status, lalu redirect.

## 4. Search bar

HTML (GET):
```php
<form action="supplier.php" method="get">
  <input type="text" name="cari" value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>">
  <button type="submit">cari</button>
</form>
```

PHP:
```php
$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
    $like = '%' . $cari . '%';
    $stmt = mysqli_prepare($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier WHERE nama_supplier LIKE ? OR kota LIKE ? ORDER BY id_supplier');
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($koneksi, 'SELECT id_supplier, nama_supplier, kota FROM supplier ORDER BY id_supplier');
}
```

`name="cari"` <-> `$_GET['cari']`. Jangan tempel input ke SQL, pakai `?`.

## 5. Nambah query / fitur baru

1. Tentukan aksi: SELECT (baca) atau INSERT/UPDATE/DELETE (ubah). Ubah data -> POST + prepared.
2. Cek DB: `SHOW TABLES; SHOW COLUMNS FROM nama_tabel;` (tambah kolom/tabel kalau perlu).
3. Tentukan input: link `?id=` -> `$_GET`; form -> `$_POST`.
4. Tulis query sesuai aksi, urutan `?` = urutan bind.
5. Output `htmlspecialchars(...)`; setelah ubah DB -> redirect + `exit`.
6. Test: `php -l file.php`, coba di browser, cek row di DB.

Contoh tambah kolom `telepon` di supplier:
- DB: `ALTER TABLE supplier ADD telepon VARCHAR(20);`
- Form: `<input name="telepon">`
- PHP: ambil `$_POST['telepon']`, masukkan ke INSERT/UPDATE + bind (tambah `s`), tampilkan di list & edit.

## 6. Checklist cepat

- Dipanggil dari mana? `href`/`action`/`require`.
- Admin? `require guard.php`.
- Input name = key `$_POST`/`$_GET`.
- Query pakai kolom/tabel yang benar.
- Ubah data = POST + prepared statement.
- Redirect setelah POST + `exit`.
