<?php
/*
 * TUGAS: supplier_edit.php
 *
 * Tujuan:
 * - Menampilkan data supplier yang akan diedit.
 * - Menyimpan perubahan nama_supplier dan kota.
 *
 * Contoh yang bisa diikuti:
 * - produk_edit.php
 *
 * Tabel: supplier
 * Field yang diedit:
 * - nama_supplier
 * - kota
 *
 * Field yang tidak boleh diedit:
 * - id_supplier (primary key)
 *
 * Yang harus dibuat:
 * 1. require __DIR__ . '/config/database.php';
 * 2. Ambil id dari URL atau POST:
 *    $id = (int) ($_GET['id_supplier'] ?? $_POST['id_supplier'] ?? 0);
 * 3. Jika id <= 0, tampilkan pesan bahwa data tidak ditemukan.
 * 4. Query data lama:
 *    SELECT id_supplier, nama_supplier, kota
 *    FROM supplier WHERE id_supplier = ?
 *    Bind dengan tipe 'i'.
 * 5. Jika data tidak ada, tampilkan pesan dan tombol kembali.
 * 6. Jika POST dan data ada:
 *    - ambil nama_supplier dan kota dengan trim()
 *    - validasi nama_supplier wajib, maksimal 50 karakter
 *    - validasi kota maksimal 30 karakter
 *    - UPDATE supplier SET nama_supplier = ?, kota = ?
 *      WHERE id_supplier = ?
 *    - bind dengan tipe 'ssi'
 *    - redirect ke supplier.php?status=edit
 * 7. Form memakai method="post" dan action="supplier_edit.php".
 * 8. Kirim id_supplier lewat input hidden.
 * 9. Tampilkan error dan nilai form dengan htmlspecialchars().
 * 10. Proses POST sebelum require header.
 */
