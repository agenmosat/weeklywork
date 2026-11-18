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
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00
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
        total DECIMAL(10,2) NOT NULL DEFAULT 0.00
    )");
    mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS pembelian_detail (
        id_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_pembelian INT NOT NULL,
        id_product INT NOT NULL,
        harga_beli DECIMAL(10,2) NOT NULL,
        qty INT NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL
    )");
}

function format_uang($angka) {
    return "$" . number_format((float)$angka, 2);
}

function generate_nomor_transaksi($prefix) {
    return strtoupper($prefix) . "-" . date("YmdHis") . "-" . rand(100, 999);
}

function hitung_total_transaksi($harga, $qty) {
    return (float)$harga * (int)$qty;
}
