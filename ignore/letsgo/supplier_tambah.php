<?php
/*
 * TUGAS: supplier_tambah.php
 *
 * Tujuan:
 * - Menampilkan form tambah supplier.
 * - Memvalidasi input.
 * - Menyimpan data baru ke tabel supplier.
 *
 * Contoh yang bisa diikuti:
 * - produk_tambah.php
 *
 * Tabel: supplier
 * Field:
 * - id_supplier   : auto increment, TIDAK diisi manual
 * - nama_supplier : wajib, maksimal 50 karakter
 * - kota          : opsional, maksimal 30 karakter
 *
 * Yang harus dibuat:
 * 1. require __DIR__ . '/config/database.php';
 * 2. Siapkan $error = []; dan nilai awal form.
 * 3. Jika REQUEST_METHOD === 'POST':
 *    - ambil nama_supplier dan kota dengan trim()
 *    - validasi nama_supplier wajib dan maksimal 50 karakter
 *    - validasi kota maksimal 30 karakter
 * 4. Jika tidak ada error, siapkan query:
 *    INSERT INTO supplier (nama_supplier, kota) VALUES (?, ?)
 * 5. Gunakan mysqli_prepare() dan mysqli_stmt_bind_param().
 *    Tipe untuk kedua field adalah 'ss'.
 * 6. Jika berhasil:
 *    header('Location: supplier.php?status=tambah');
 *    exit;
 * 7. Proses POST HARUS selesai sebelum require header.
 * 8. Tampilkan error dengan alert Bootstrap dan htmlspecialchars().
 * 9. Form memakai method="post" dan action="supplier_tambah.php".
 * 10. Sertakan tombol Simpan dan Batal.
 */
