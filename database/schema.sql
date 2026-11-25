DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS sale_details;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS purchase_details;
DROP TABLE IF EXISTS purchases;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS cashiers;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(30)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(50)  NOT NULL,
  role ENUM('admin','kasir','cashier','user') DEFAULT 'kasir',
  id_kasir INT NULL,
  status ENUM('aktif','nonaktif') DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL,
    category VARCHAR(50) NOT NULL DEFAULT '',
    code VARCHAR(50) NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    unit VARCHAR(50) NOT NULL,
    price DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 0,
    lokasi VARCHAR(50) DEFAULT 'Surabaya',
    rating DECIMAL(2,1) DEFAULT 4.9,
    terjual INT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'images/no-image.webp',
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sales (
    id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    nama_pelanggan VARCHAR(100) NOT NULL,
    total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) NOT NULL DEFAULT 'selesai'
);

CREATE TABLE sale_details (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL,
    id_product INT NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL
);

CREATE TABLE purchases (
    id_pembelian INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_supplier INT NOT NULL,
    total DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(20) NOT NULL DEFAULT 'selesai'
);

CREATE TABLE purchase_details (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pembelian INT NOT NULL,
    id_product INT NOT NULL,
    harga_beli DECIMAL(15,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL
);

CREATE TABLE stock_movements (
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

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_pesanan VARCHAR(50) NOT NULL,
    nama_pembeli VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    total DECIMAL(15,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    status_pembayaran VARCHAR(50) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cashiers (
    id_kasir INT AUTO_INCREMENT PRIMARY KEY,
    nama_kasir VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    tanggal_keluar DATE DEFAULT NULL
);

CREATE TABLE suppliers (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(50) UNIQUE NOT NULL,
    kota VARCHAR(30) DEFAULT NULL
);

INSERT INTO users (username, password, name, role, id_kasir, status)
VALUES (
  'admin',
  '$2y$10$RyFoVYnot0wdhwwO1s5xeeprLka1KLvg.cR0seFstrmG338MtKdtG',
  'Pemilik Toko',
  'admin',
  NULL,
  'aktif'
)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  role         = VALUES(role),
  status       = VALUES(status);

INSERT INTO users (username, password, name, role, id_kasir, status)
VALUES (
  'kasir1',
  '$2y$10$xzZL9oLAghDYz3nBrZpyWOx/JAZdE9SsYC7yyFKjTXcQAfi00LK02',
  'Kasir Satu',
  'kasir',
  1,
  'aktif'
)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  role         = VALUES(role),
  id_kasir     = VALUES(id_kasir),
  status       = VALUES(status);

INSERT INTO categories (name, description) VALUES
('Arduino', 'Board dan perangkat pengembangan Arduino'),
('ESP', 'Board berbasis ESP8266 dan ESP32'),
('Sensor', 'Berbagai jenis sensor untuk project IoT'),
('Module', 'Modul komunikasi dan elektronik'),
('Display', 'LCD, OLED, dan perangkat display'),
('Actuator', 'Motor, relay, servo, dan perangkat output'),
('Kit', 'Paket lengkap untuk project IoT'),
('Component', 'Komponen elektronik pendukung');

INSERT INTO products
(category_id, code, name, description, unit, price, stock, minimum_stock, status)
VALUES
(1, 'ARD-001', 'Arduino Uno R3',
 'Board mikrokontroler Arduino Uno R3 untuk berbagai project elektronik dan IoT',
 'pcs', 125000, 20, 5, 'active'),
(1, 'ARD-002', 'Arduino Uno R3 Set',
 'Paket Arduino Uno R3 lengkap dengan komponen pendukung untuk belajar dan membuat project',
 'set', 185000, 15, 3, 'active'),
(1, 'ARD-003', 'Arduino Nano',
 'Board Arduino Nano berukuran kecil untuk project elektronik',
 'pcs', 85000, 25, 5, 'active'),
(1, 'ARD-004', 'Arduino Mega 2560',
 'Board mikrokontroler dengan jumlah pin yang lebih banyak untuk project kompleks',
 'pcs', 175000, 10, 3, 'active'),
(2, 'ESP-001', 'ESP32 DevKit V1',
 'Development board ESP32 dengan WiFi dan Bluetooth',
 'pcs', 95000, 20, 5, 'active'),
(2, 'ESP-002', 'ESP8266 NodeMCU',
 'Development board berbasis ESP8266 dengan konektivitas WiFi',
 'pcs', 65000, 18, 5, 'active'),
(2, 'ESP-003', 'ESP32-CAM',
 'Board ESP32 dengan kamera untuk project IoT dan monitoring',
 'pcs', 110000, 12, 3, 'active'),
(3, 'SNS-001', 'DHT11 Temperature Sensor',
 'Sensor untuk mengukur suhu dan kelembapan',
 'pcs', 18000, 30, 5, 'active'),
(3, 'SNS-002', 'DHT22 Temperature Sensor',
 'Sensor suhu dan kelembapan dengan tingkat akurasi lebih tinggi',
 'pcs', 45000, 20, 5, 'active'),
(3, 'SNS-003', 'HC-SR04 Ultrasonic Sensor',
 'Sensor ultrasonik untuk mengukur jarak',
 'pcs', 22000, 25, 5, 'active'),
(3, 'SNS-004', 'LDR Light Sensor',
 'Sensor untuk mendeteksi intensitas cahaya',
 'pcs', 10000, 30, 5, 'active'),
(3, 'SNS-005', 'PIR Motion Sensor',
 'Sensor untuk mendeteksi pergerakan',
 'pcs', 18000, 25, 5, 'active'),
(3, 'SNS-006', 'MQ-2 Gas Sensor',
 'Sensor untuk mendeteksi gas dan asap',
 'pcs', 25000, 20, 5, 'active'),
(4, 'MOD-001', 'ESP8266 WiFi Module',
 'Modul WiFi untuk menambahkan konektivitas jaringan',
 'pcs', 55000, 15, 3, 'active'),
(4, 'MOD-002', 'RFID RC522 Module',
 'Modul RFID untuk membaca kartu dan tag RFID',
 'pcs', 30000, 25, 5, 'active'),
(4, 'MOD-003', 'Bluetooth HC-05',
 'Modul Bluetooth untuk komunikasi wireless',
 'pcs', 45000, 20, 5, 'active'),
(4, 'MOD-004', 'Relay 2 Channel',
 'Modul relay dua channel untuk mengontrol perangkat listrik',
 'pcs', 25000, 20, 5, 'active'),
(5, 'DSP-001', 'LCD 16x2 I2C',
 'Display LCD 16x2 dengan interface I2C',
 'pcs', 30000, 20, 5, 'active'),
(5, 'DSP-002', 'OLED 0.96 Inch I2C',
 'Display OLED kecil dengan interface I2C',
 'pcs', 35000, 20, 5, 'active'),
(6, 'ACT-001', 'SG90 Servo Motor',
 'Motor servo mini untuk project robotik dan IoT',
 'pcs', 18000, 30, 5, 'active'),
(6, 'ACT-002', 'DC Motor 5V',
 'Motor DC 5V untuk project elektronik',
 'pcs', 15000, 30, 5, 'active'),
(6, 'ACT-003', 'Buzzer Module',
 'Modul buzzer untuk menghasilkan suara atau alarm',
 'pcs', 10000, 30, 5, 'active'),
(7, 'KIT-001', 'Arduino Starter Kit',
 'Paket belajar Arduino dengan board dan berbagai komponen elektronik',
 'set', 250000, 10, 2, 'active'),
(7, 'KIT-002', 'ESP32 IoT Starter Kit',
 'Paket project IoT berbasis ESP32 dengan sensor dan modul pendukung',
 'set', 275000, 8, 2, 'active'),
(7, 'KIT-003', 'Arduino Sensor Kit',
 'Paket berbagai sensor untuk project Arduino',
 'set', 175000, 10, 2, 'active'),
(8, 'CMP-001', 'Breadboard 830 Point',
 'Breadboard untuk merangkai rangkaian elektronik tanpa solder',
 'pcs', 25000, 25, 5, 'active'),
(8, 'CMP-002', 'Jumper Wire Male-Male',
 'Kabel jumper male to male untuk koneksi breadboard',
 'pack', 15000, 30, 5, 'active'),
(8, 'CMP-003', 'Jumper Wire Male-Female',
 'Kabel jumper male to female untuk koneksi modul elektronik',
 'pack', 15000, 30, 5, 'active'),
(8, 'CMP-004', 'Resistor Kit',
 'Paket resistor dengan berbagai nilai resistansi',
 'pack', 30000, 20, 5, 'active'),
(8, 'CMP-005', 'LED Assorted Kit',
 'Paket LED dengan berbagai warna',
 'pack', 20000, 25, 5, 'active');

UPDATE products p JOIN categories c ON p.category_id = c.id SET p.category = c.name WHERE p.category = '';
