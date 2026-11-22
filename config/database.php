<?php
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'toko';

if (!function_exists('mysqli_connect')) {
    die('ekstensi mysqli belum aktif di php. aktifkan extension=mysqli di php.ini atau jalankan server dengan: php -d extension=mysqli -S localhost:8000');
}

$sock = file_exists('/opt/lampp/var/mysql/mysql.sock') ? '/opt/lampp/var/mysql/mysql.sock' : (file_exists('/tmp/mysql.sock') ? '/tmp/mysql.sock' : null);

$koneksi = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306, $sock);
if (!$koneksi) {
    $koneksi = @mysqli_connect('127.0.0.1', DB_USER, DB_PASS, DB_NAME, 3306);
}
if (!$koneksi) {
    $root = @mysqli_connect('127.0.0.1', DB_USER, DB_PASS, '', 3306) ?: @mysqli_connect(DB_HOST, DB_USER, DB_PASS, '', 3306, $sock);
    if ($root) {
        mysqli_query($root, "CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        mysqli_close($root);
        $koneksi = @mysqli_connect('127.0.0.1', DB_USER, DB_PASS, DB_NAME, 3306) ?: @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306, $sock);
    }
}

if (!$koneksi) {
    die('koneksi database gagal: ' . mysqli_connect_error());
}

$chk_prod = @mysqli_query($koneksi, "SHOW TABLES LIKE 'products'");
if ($chk_prod && mysqli_num_rows($chk_prod) === 0) {
    $schemafile = __DIR__ . '/../schema.sql';
    if (file_exists($schemafile)) {
        $sql = file_get_contents($schemafile);
        if ($sql !== false && @mysqli_multi_query($koneksi, $sql)) {
            while (@mysqli_more_results($koneksi) && @mysqli_next_result($koneksi)) {
            }
        }
    }
} else {
    $chk_col = @mysqli_query($koneksi, "SHOW COLUMNS FROM products LIKE 'lokasi'");
    if ($chk_col && mysqli_num_rows($chk_col) === 0) {
        @mysqli_query($koneksi, "ALTER TABLE products ADD COLUMN lokasi VARCHAR(50) DEFAULT 'Surabaya'");
        @mysqli_query($koneksi, "ALTER TABLE products ADD COLUMN rating DECIMAL(2,1) DEFAULT 4.9");
        @mysqli_query($koneksi, "ALTER TABLE products ADD COLUMN terjual INT DEFAULT 0");
    }
}
