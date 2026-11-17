CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'images/glock17.png'
);

CREATE TABLE kasir (
    id_kasir INT AUTO_INCREMENT PRIMARY KEY,
    nama_kasir VARCHAR(50) NOT NULL UNIQUE,
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
    nama_pembeli VARCHAR(100) NOT NULL UNIQUE,
    alamat TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    status_pembayaran VARCHAR(50) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, price, stock, category, description, image) VALUES
('glock 19 gen 5', 520.00, 14, 'pistol', 'standard sidearm', 'images/glock17.png');

INSERT INTO kasir (nama_kasir, status) VALUES
('kasir1', 'aktif'),
('kasir2', 'nonaktif');

INSERT INTO supplier (nama_supplier, kota) VALUES
('supplier1', 'jakarta'),
('supplier2', 'bandung');

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
