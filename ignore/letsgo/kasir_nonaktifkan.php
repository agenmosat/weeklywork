<?php
/*
 * TUGAS: kasir_nonaktifkan.php
 *
 * Tujuan:
 * - Menampilkan konfirmasi sebelum menonaktifkan kasir.
 * - Mengubah status kasir menjadi 'nonaktif'.
 * - Mengisi tanggal_keluar dengan tanggal hari ini.
 *
 * Contoh yang bisa diikuti:
 * - produk_hapus.php (untuk pola konfirmasi GET lalu POST)
 *
 * Tabel: kasir
 * Field yang diubah:
 * - status         : menjadi 'nonaktif'
 * - tanggal_keluar : diisi CURDATE()
 *
 * PENTING:
 * - JANGAN memakai DELETE FROM kasir.
 * - Riwayat penjualan dan pembelian harus tetap utuh.
 *
 * Yang harus dibuat:
 * 1. require __DIR__ . '/config/database.php';
 * 2. Ambil id_kasir dari GET atau POST, ubah ke integer.
 * 3. Query data kasir:
 *    SELECT id_kasir, nama_kasir, status, tanggal_keluar
 *    FROM kasir WHERE id_kasir = ?
 * 4. Tampilkan data kasir dalam tabel konfirmasi.
 * 5. Jika POST dan data ada, jalankan:
 *    UPDATE kasir
 *    SET status = 'nonaktif', tanggal_keluar = CURDATE()
 *    WHERE id_kasir = ?
 *    Bind dengan tipe 'i'.
 * 6. Jika berhasil:
 *    header('Location: kasir.php?status=nonaktif');
 *    exit;
 * 7. Jika gagal, tampilkan pesan error.
 * 8. Tombol konfirmasi harus berada di dalam form method="post".
 * 9. Sertakan tombol Batal menuju kasir.php.
 * 10. Proses POST sebelum require header.
 */
