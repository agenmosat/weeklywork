CREATE DATABASE IF NOT EXISTS toko;
USE toko;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL UNIQUE,
    price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    category VARCHAR(50) NOT NULL,
    jenis ENUM('Material','Alat Berat') NOT NULL DEFAULT 'Material',
    satuan VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'images/no-image.webp'
);

CREATE TABLE kasir (
    id_kasir INT AUTO_INCREMENT PRIMARY KEY,
    nama_kasir VARCHAR(50) NOT NULL,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    tanggal_keluar DATE DEFAULT NULL
);

CREATE TABLE supplier (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(50) NOT NULL UNIQUE,
    kota VARCHAR(30) DEFAULT NULL
);

CREATE TABLE pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_pesanan VARCHAR(50) NOT NULL,
    nama_pembeli VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    status_pembayaran VARCHAR(50) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (kode, name, category, satuan, description) VALUES
('SEM-001','Semen Portland 50kg','Semen','Sak',''),
('SEM-002','Semen Mortar 40kg','Semen','Sak',''),
('PAS-001','Pasir Bangunan','Pasir','m³',''),
('PAS-002','Pasir Beton','Pasir','m³',''),
('BAT-001','Bata Merah','Bata','Buah',''),
('BAT-002','Batako Press','Batako','Buah',''),
('BET-001','Beton Ready Mix K-225','Beton','m³',''),
('BES-001','Besi Beton 8mm','Besi & Baja','Batang',''),
('BES-002','Besi Beton 10mm','Besi & Baja','Batang',''),
('BES-003','Besi Beton 12mm','Besi & Baja','Batang',''),
('BES-004','Besi Beton 16mm','Besi & Baja','Batang',''),
('KAY-001','Kayu Balok 5x10','Kayu','Batang',''),
('KAY-002','Kayu Papan','Kayu','Lembar',''),
('PIP-001','Pipa PVC 1/2"','Pipa','Batang',''),
('PIP-002','Pipa PVC 1"','Pipa','Batang',''),
('ATAP-001','Seng Gelombang','Atap','Lembar',''),
('ATAP-002','Genteng Metal','Atap','Lembar',''),
('CAT-001','Cat Tembok 5kg','Cat','Kaleng',''),
('CAT-002','Cat Eksterior 20kg','Cat','Kaleng',''),
('KER-001','Keramik 40x40','Keramik','Dus','');

INSERT INTO products (kode, name, category, jenis, satuan, description) VALUES
('EXC-001','Excavator 20 Ton','Excavator','Alat Berat','Unit',''),
('EXC-002','Mini Excavator 5 Ton','Excavator','Alat Berat','Unit',''),
('EXC-003','Excavator 30 Ton','Excavator','Alat Berat','Unit',''),
('CRN-001','Mobile Crane 25 Ton','Crane','Alat Berat','Unit',''),
('CRN-002','Crawler Crane 50 Ton','Crane','Alat Berat','Unit',''),
('CRN-003','Tower Crane','Crane','Alat Berat','Unit',''),
('BLD-001','Bulldozer D6','Bulldozer','Alat Berat','Unit',''),
('BLD-002','Bulldozer D8','Bulldozer','Alat Berat','Unit',''),
('WHL-001','Wheel Loader 2.5 m³','Wheel Loader','Alat Berat','Unit',''),
('WHL-002','Wheel Loader 3 m³','Wheel Loader','Alat Berat','Unit',''),
('FLT-001','Forklift 3 Ton','Forklift','Alat Berat','Unit',''),
('FLT-002','Forklift 5 Ton','Forklift','Alat Berat','Unit',''),
('DMP-001','Dump Truck 10 Ton','Dump Truck','Alat Berat','Unit',''),
('DMP-002','Dump Truck 20 Ton','Dump Truck','Alat Berat','Unit',''),
('MIX-001','Concrete Mixer 0.5 m³','Concrete Mixer','Alat Berat','Unit',''),
('CMP-001','Vibro Roller 10 Ton','Compactor','Alat Berat','Unit',''),
('GRD-001','Motor Grader','Motor Grader','Alat Berat','Unit',''),
('ASP-001','Asphalt Paver','Asphalt Paver','Alat Berat','Unit',''),
('PUM-001','Concrete Pump Truck','Concrete Pump','Alat Berat','Unit',''),
('GEN-001','Genset 50 KVA','Genset','Alat Berat','Unit',''),
('GEN-002','Genset 100 KVA','Genset','Alat Berat','Unit','');

INSERT INTO kasir (nama_kasir, status) VALUES
('kasir1', 'aktif'),
('kasir2', 'nonaktif');

INSERT INTO supplier (nama_supplier, kota) VALUES
('supplier1', 'kota');

CREATE TABLE IF NOT EXISTS penjualan (
    id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    nama_pelanggan VARCHAR(100) NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

CREATE TABLE IF NOT EXISTS penjualan_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL,
    id_product INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS pembelian (
    id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_supplier INT NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00
);

CREATE TABLE IF NOT EXISTS pembelian_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pembelian INT NOT NULL,
    id_product INT NOT NULL,
    harga_beli DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS stok (
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
);

CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin', 'cashier', 'kasir') NOT NULL,
    id_kasir INT DEFAULT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO pengguna (username, password, nama, role, id_kasir, status) VALUES
('admin', '$2y$12$rvxiPNaTRPv4YEz.6YgbVu7zVdDL90zP1pzm35iFG79lzDRtC.p1u', 'Administrator', 'admin', NULL, 'aktif'),
('kasir1', '$2y$12$aMCPEfOnFaub6wgdVCmuNOSEuCtJZgy44HPFaZo2VyH3pA8LXWVeW', 'Kasir Satu', 'cashier', 1, 'aktif')
ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role), id_kasir = VALUES(id_kasir), status = VALUES(status);

