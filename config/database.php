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

$koneksi = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$koneksi) {
    $koneksi = @mysqli_connect('127.0.0.1', DB_USER, DB_PASS, DB_NAME, 3306);
}

if (!$koneksi) {
    die('koneksi database gagal: ' . mysqli_connect_error());
}
