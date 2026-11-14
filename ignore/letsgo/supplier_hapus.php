<?php
/*
 * TUGAS: supplier_hapus.php
 *
 * Tujuan:
 * - Menampilkan konfirmasi hapus supplier.
 * - Mencegah penghapusan supplier yang sudah dipakai di tabel pembelian.
 * - Menghapus supplier hanya jika belum pernah dipakai.
 *
 * Contoh yang bisa diikuti:
 * - produk_hapus.php (untuk pola konfirmasi GET lalu POST)
 *
 * Tabel: supplier
 * Primary key: id_supplier
 *
 * Yang harus dibuat:
 * 1. require __DIR__ . '/config/database.php';
 * 2. Ambil id_supplier dari GET atau POST, ubah ke integer.
 * 3. Query data supplier:
 *    SELECT id_supplier, nama_supplier, kota
 *    FROM supplier WHERE id_supplier = ?
 * 4. Tampilkan data supplier dalam tabel konfirmasi.
 * 5. Jika POST dan data ada, cek pemakaian:
 *    SELECT COUNT(*) AS jumlah
 *    FROM pembelian
 *    WHERE id_supplier = ?
 *    Bind dengan tipe 'i'.
 * 6. Jika jumlah > 0:
 *    - JANGAN hapus.
 *    - Tampilkan pesan: supplier sudah dipakai di pembelian.
 * 7. Jika jumlah === 0:
 *    DELETE FROM supplier WHERE id_supplier = ?
 *    Bind dengan tipe 'i'.
 * 8. Jika berhasil:
 *    header('Location: supplier.php?status=hapus');
 *    exit;
 * 9. Tombol konfirmasi harus berada di dalam form method="post".
 * 10. Sertakan tombol Batal menuju supplier.php.
 * 11. Proses POST sebelum require header.
 */
