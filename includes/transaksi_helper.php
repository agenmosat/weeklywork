<?php
function init_transaksi_tables($koneksi) {
    if (!$koneksi) {
        return;
    }
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS penjualan (
        id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
        nomor_transaksi VARCHAR(50) NOT NULL UNIQUE,
        tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
        nama_pelanggan VARCHAR(100) NOT NULL,
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status VARCHAR(20) NOT NULL DEFAULT 'selesai'
    )");
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS penjualan_detail (
        id_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_penjualan INT NOT NULL,
        id_product INT NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        qty INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL
    )");
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS pembelian (
        id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
        nomor_transaksi VARCHAR(50) NOT NULL UNIQUE,
        tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
        id_supplier INT NOT NULL,
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        status VARCHAR(20) NOT NULL DEFAULT 'selesai'
    )");
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS pembelian_detail (
        id_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_pembelian INT NOT NULL,
        id_product INT NOT NULL,
        harga_beli DECIMAL(10,2) NOT NULL,
        qty INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL
    )");
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS stok (
        id_stok INT AUTO_INCREMENT PRIMARY KEY,
        id_product INT NOT NULL,
        tanggal DATETIME NOT NULL,
        jumlah_masuk INT NOT NULL DEFAULT 0,
        jumlah_keluar INT NOT NULL DEFAULT 0,
        saldo_awal INT NOT NULL DEFAULT 0,
        saldo_akhir INT NOT NULL DEFAULT 0,
        no_penjualan VARCHAR(50) NULL,
        no_pembelian VARCHAR(50) NULL,
        keterangan VARCHAR(255) NULL
    )");
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS pengguna (
        id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nama VARCHAR(100) NOT NULL,
        role ENUM('admin', 'cashier', 'kasir') NOT NULL,
        id_kasir INT DEFAULT NULL,
        status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $chk_admin = mysqli_query($koneksi, "SELECT id_pengguna FROM pengguna WHERE username = 'admin' LIMIT 1");
    if ($chk_admin && mysqli_num_rows($chk_admin) === 0) {
        $hash_admin = password_hash('admin123', PASSWORD_BCRYPT);
        mysqli_query($koneksi, "INSERT INTO pengguna (username, password, nama, role, id_kasir, status) VALUES ('admin', '$hash_admin', 'Administrator', 'admin', NULL, 'aktif')");
    }
    $chk_kasir = mysqli_query($koneksi, "SELECT id_pengguna FROM pengguna WHERE username = 'kasir1' LIMIT 1");
    if ($chk_kasir && mysqli_num_rows($chk_kasir) === 0) {
        $hash_kasir = password_hash('kasir123', PASSWORD_BCRYPT);
        mysqli_query($koneksi, "INSERT INTO pengguna (username, password, nama, role, id_kasir, status) VALUES ('kasir1', '$hash_kasir', 'Kasir Satu', 'cashier', 1, 'aktif')");
    }
}

function format_uang($angka) {
    return "$" . number_format((float)$angka, 2);
}

function hitung_total_transaksi($harga, $qty) {
    return (float)$harga * (int)$qty;
}

function buat_nomor($koneksi, $prefix, $tabel, $kolom) {
    $clean_prefix = rtrim(strtoupper(trim($prefix)), '-') . '-';
    if (!$koneksi) {
        return $clean_prefix . '0001';
    }
    $tabel = preg_replace('/[^a-zA-Z0-9_]/', '', $tabel);
    $kolom = preg_replace('/[^a-zA-Z0-9_]/', '', $kolom);
    $q = mysqli_query($koneksi, "SELECT $kolom FROM $tabel WHERE $kolom LIKE '$clean_prefix%' ORDER BY $kolom DESC LIMIT 1");
    if ($q && ($row = mysqli_fetch_assoc($q))) {
        $last_num = (int) preg_replace('/\D/', '', $row[$kolom]);
        $next = $last_num + 1;
    } else {
        $next = 1;
    }
    return $clean_prefix . sprintf('%04d', $next);
}

function saldo_stok_terakhir($koneksi, $id_product) {
    $id = (int)$id_product;
    if (!$koneksi || $id <= 0) {
        return 0;
    }
    $q = mysqli_query($koneksi, "SELECT saldo_akhir FROM stok WHERE id_product = $id ORDER BY id_stok DESC LIMIT 1");
    if ($q && ($row = mysqli_fetch_assoc($q))) {
        return (int)$row['saldo_akhir'];
    }
    $qp = mysqli_query($koneksi, "SELECT stock FROM products WHERE id = $id");
    if ($qp && ($rowp = mysqli_fetch_assoc($qp))) {
        return (int)$rowp['stock'];
    }
    return 0;
}

function catat_mutasi_stok($koneksi, $id_product, $masuk, $keluar, $no_pj = null, $no_pb = null, $keterangan = '') {
    $id = (int)$id_product;
    $masuk = (int)$masuk;
    $keluar = (int)$keluar;
    $saldo_awal = saldo_stok_terakhir($koneksi, $id);
    $saldo_akhir = $saldo_awal + $masuk - $keluar;
    $tanggal = date('Y-m-d H:i:s');
    $no_pj_val = $no_pj ? "'" . mysqli_real_escape_string($koneksi, $no_pj) . "'" : "NULL";
    $no_pb_val = $no_pb ? "'" . mysqli_real_escape_string($koneksi, $no_pb) . "'" : "NULL";
    $ket_val = mysqli_real_escape_string($koneksi, $keterangan);

    $sql = "INSERT INTO stok (id_product, tanggal, jumlah_masuk, jumlah_keluar, saldo_awal, saldo_akhir, no_penjualan, no_pembelian, keterangan) 
            VALUES ($id, '$tanggal', $masuk, $keluar, $saldo_awal, $saldo_akhir, $no_pj_val, $no_pb_val, '$ket_val')";
    $ins = mysqli_query($koneksi, $sql);
    if ($ins) {
        mysqli_query($koneksi, "UPDATE products SET stock = $saldo_akhir WHERE id = $id");
        return true;
    }
    return false;
}

function batal_pembelian($koneksi, $id_pembelian) {
    $id = (int)$id_pembelian;
    if (!$koneksi || $id <= 0) {
        return false;
    }
    $q_pb = mysqli_query($koneksi, "SELECT nomor_transaksi, status FROM pembelian WHERE id_pembelian = $id");
    $pb = $q_pb ? mysqli_fetch_assoc($q_pb) : null;
    if (!$pb || ($pb['status'] ?? '') === 'batal') {
        return false;
    }
    $q_det = mysqli_query($koneksi, "SELECT id_product, qty FROM pembelian_detail WHERE id_pembelian = $id");
    $items = [];
    while ($row = mysqli_fetch_assoc($q_det)) {
        $items[] = $row;
        $cur = saldo_stok_terakhir($koneksi, $row['id_product']);
        if ($cur < (int)$row['qty']) {
            return false;
        }
    }
    if (empty($items)) {
        return false;
    }
    mysqli_begin_transaction($koneksi);
    try {
        $no = $pb['nomor_transaksi'];
        foreach ($items as $it) {
            catat_mutasi_stok($koneksi, $it['id_product'], 0, $it['qty'], null, $no, 'Batal pembelian ' . $no);
        }
        mysqli_query($koneksi, "UPDATE pembelian SET status = 'batal' WHERE id_pembelian = $id");
        mysqli_commit($koneksi);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        return false;
    }
}

function batal_penjualan($koneksi, $id_penjualan) {
    $id = (int)$id_penjualan;
    if (!$koneksi || $id <= 0) {
        return false;
    }
    $q_pj = mysqli_query($koneksi, "SELECT nomor_transaksi, status FROM penjualan WHERE id_penjualan = $id");
    $pj = $q_pj ? mysqli_fetch_assoc($q_pj) : null;
    if (!$pj || ($pj['status'] ?? '') === 'batal') {
        return false;
    }
    $q_det = mysqli_query($koneksi, "SELECT id_product, qty FROM penjualan_detail WHERE id_penjualan = $id");
    $items = [];
    while ($row = mysqli_fetch_assoc($q_det)) {
        $items[] = $row;
    }
    if (empty($items)) {
        return false;
    }
    mysqli_begin_transaction($koneksi);
    try {
        $no = $pj['nomor_transaksi'];
        foreach ($items as $it) {
            catat_mutasi_stok($koneksi, $it['id_product'], $it['qty'], 0, $no, null, 'Batal penjualan ' . $no);
        }
        mysqli_query($koneksi, "UPDATE penjualan SET status = 'batal' WHERE id_penjualan = $id");
        mysqli_commit($koneksi);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        return false;
    }
}
