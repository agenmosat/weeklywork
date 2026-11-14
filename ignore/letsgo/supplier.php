<?php
/*
 * TUGAS: supplier.php
 *
 * Tujuan:
 * - Menampilkan daftar supplier dari tabel supplier.
 * - Menyediakan tombol Edit dan Hapus.
 * - Menampilkan pesan status setelah tambah/edit/hapus.
 *
 * Contoh yang bisa diikuti:
 * - produk.php
 *
 * Tabel: supplier
 * Field:
 * - id_supplier   : auto increment, primary key
 * - nama_supplier : nama perusahaan/pemasok
 * - kota          : kota supplier
 *
 * Yang harus dibuat:
 * 1. require __DIR__ . '/config/database.php';
 * 2. Query:
 *    SELECT id_supplier, nama_supplier, kota
 *    FROM supplier
 *    ORDER BY id_supplier
 * 3. Tampilkan data dalam tabel Bootstrap.
 * 4. Tombol Edit menuju:
 *    supplier_edit.php?id_supplier=...
 * 5. Tombol Hapus menuju:
 *    supplier_hapus.php?id_supplier=...
 * 6. Tampilkan pesan dari query string ?status=tambah|edit|hapus.
 * 7. Gunakan require __DIR__ . '/partials/header.php'; sebelum HTML.
 * 8. Gunakan require __DIR__ . '/partials/footer.php'; di akhir halaman.
 * 9. Semua output teks memakai htmlspecialchars().
 *
 * Catatan:
 * - Hapus supplier hanya boleh jika supplier belum dipakai di tabel pembelian.
 * - Pengecekan dilakukan di supplier_hapus.php.
 */
