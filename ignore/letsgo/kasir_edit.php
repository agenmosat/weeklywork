<?php
/*
 * TUGAS: kasir_edit.php
 *
 * Tujuan:
 * - Menampilkan data kasir yang akan diedit.
 * - Menyimpan perubahan nama kasir.
 *
 * Contoh yang bisa diikuti:
 * - produk_edit.php
 *
 * Tabel: kasir
 * Field yang diedit:
 * - nama_kasir
 *
 * Field yang tidak boleh diedit:
 * - id_kasir (primary key)
 *
 * Yang harus dibuat:
 * 1. require __DIR__ . '/config/database.php';
 * 2. Ambil id dari URL atau POST:
 *    $id = (int) ($_GET['id_kasir'] ?? $_POST['id_kasir'] ?? 0);
 * 3. Jika id <= 0, tampilkan pesan bahwa data tidak ditemukan.
 * 4. Query data lama:
 *    SELECT id_kasir, nama_kasir, status, tanggal_keluar
 *    FROM kasir WHERE id_kasir = ?
 *    Bind dengan tipe 'i'.
 * 5. Jika data tidak ada, tampilkan pesan dan tombol kembali.
 * 6. Jika POST dan data ada:
 *    - ambil nama_kasir dengan trim()
 *    - validasi wajib diisi, maksimal 50 karakter
 *    - UPDATE kasir SET nama_kasir = ? WHERE id_kasir = ?
 *    - bind dengan tipe 'si'
 *    - redirect ke kasir.php?status=edit
 * 7. Form memakai method="post" dan action="kasir_edit.php".
 * 8. Kirim id_kasir lewat input hidden.
 * 9. Tampilkan error dan nilai form dengan htmlspecialchars().
 * 10. Proses POST sebelum require header.
 */
