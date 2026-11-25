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

INSERT INTO categories (id, name, description) VALUES
(1, 'Semen', 'Kategori aneka semen dan perekat'),
(2, 'Pasir', 'Kategori pasir bangunan'),
(3, 'Bata', 'Kategori bata merah dan hebel'),
(4, 'Batako', 'Kategori batako'),
(5, 'Beton', 'Kategori cor beton'),
(6, 'Besi & Baja', 'Kategori besi beton dan baja'),
(7, 'Kayu', 'Kategori balok dan papan kayu'),
(8, 'Pipa', 'Kategori pipa dan instalasi air'),
(9, 'Atap', 'Kategori seng dan genteng'),
(10, 'Cat', 'Kategori cat tembok dan eksterior'),
(11, 'Keramik', 'Kategori keramik lantai'),
(12, 'Excavator', 'Kategori alat berat excavator'),
(13, 'Crane', 'Kategori derek dan crane'),
(14, 'Bulldozer', 'Kategori bulldozer'),
(15, 'Wheel Loader', 'Kategori wheel loader'),
(16, 'Forklift', 'Kategori forklift'),
(17, 'Dump Truck', 'Kategori truk dump'),
(18, 'Concrete Mixer', 'Kategori mixer beton'),
(19, 'Compactor', 'Kategori pemadat jalan'),
(20, 'Motor Grader', 'Kategori mesin perata tanah'),
(21, 'Asphalt Paver', 'Kategori pengaspal jalan'),
(22, 'Concrete Pump', 'Kategori pompa beton'),
(23, 'Genset', 'Kategori generator genset');

INSERT INTO products (category_id, code, name, description, unit, price, stock, minimum_stock, image, status) VALUES
(1, 'SEM-001', 'Semen Portland 50kg', 'Semen portland serbaguna', 'Sak', 65000.00, 100, 10, '../home/images/barang/Sportlan50kg.webp', 'active'),
(1, 'SEM-002', 'Semen Mortar 40kg', 'Semen instan siap pakai', 'Sak', 85000.00, 80, 10, '../home/images/barang/Smortar40kg.webp', 'active'),
(2, 'PAS-001', 'Pasir Bangunan', 'Pasir pasang bangunan berkualitas', 'm³', 250000.00, 20, 5, '../home/images/barang/pasirbangunan.webp', 'active'),
(2, 'PAS-002', 'Pasir Beton', 'Pasir khusus cor beton', 'm³', 280000.00, 15, 5, '../home/images/barang/pasirbeton.webp', 'active'),
(3, 'BAT-001', 'Bata Merah', 'Bata merah bakar standar', 'Buah', 800.00, 5000, 500, '../home/images/barang/batamerah.webp', 'active'),
(4, 'BAT-002', 'Batako Press', 'Batako semen pres padat', 'Buah', 3500.00, 2000, 200, '../home/images/barang/batakopressireng.webp', 'active'),
(5, 'BET-001', 'Beton Ready Mix K-225', 'Beton curah mutu K-225', 'm³', 850000.00, 10, 2, '../home/images/barang/betonmutuk225.webp', 'active'),
(6, 'BES-001', 'Besi Beton 8mm', 'Besi polos SNI 8mm', 'Batang', 45000.00, 300, 30, '../home/images/barang/besibeton8mm.webp', 'active'),
(6, 'BES-002', 'Besi Beton 10mm', 'Besi polos SNI 10mm', 'Batang', 72000.00, 250, 25, '../home/images/barang/besibeton10mm.webp', 'active'),
(6, 'BES-003', 'Besi Beton 12mm', 'Besi ulir SNI 12mm', 'Batang', 105000.00, 200, 20, '../home/images/barang/besibeton12mm.webp', 'active'),
(6, 'BES-004', 'Besi Beton 16mm', 'Besi ulir SNI 16mm', 'Batang', 185000.00, 150, 15, '../home/images/barang/besibeton16mm.webp', 'active'),
(7, 'KAY-001', 'Kayu Balok 5x10', 'Kayu balok meranti panjang 4 meter', 'Batang', 65000.00, 100, 10, '../home/images/barang/kayubalok5x10.webp', 'active'),
(7, 'KAY-002', 'Kayu Papan', 'Kayu papan ketebalan 3cm', 'Lembar', 45000.00, 120, 15, '../home/images/barang/kayupapan.webp', 'active'),
(8, 'PIP-001', 'Pipa PVC 1/2"', 'Pipa PVC saluran air bersih', 'Batang', 28000.00, 100, 10, '../home/images/barang/pipapvc12.webp', 'active'),
(8, 'PIP-002', 'Pipa PVC 1"', 'Pipa PVC standar SNI', 'Batang', 42000.00, 80, 10, '../home/images/barang/pipapvc1.webp', 'active'),
(9, 'ATAP-001', 'Seng Gelombang', 'Seng atap gelombang 180cm', 'Lembar', 55000.00, 90, 10, '../home/images/barang/senggelombang.webp', 'active'),
(9, 'ATAP-002', 'Genteng Metal', 'Genteng metal lapis pasir', 'Lembar', 48000.00, 150, 20, '../home/images/barang/gentengmetal.webp', 'active'),
(10, 'CAT-001', 'Cat Tembok 5kg', 'Cat tembok interior putih bersih', 'Kaleng', 120000.00, 50, 5, '../home/images/barang/cattembok5kg.webp', 'active'),
(10, 'CAT-002', 'Cat Eksterior 20kg', 'Cat tahan cuaca luar ruangan', 'Kaleng', 480000.00, 25, 3, '../home/images/barang/cat20kg.webp', 'active'),
(11, 'KER-001', 'Keramik 40x40', 'Keramik lantai motif marmer', 'Dus', 65000.00, 80, 10, '../home/images/barang/keramik.webp', 'active'),
(12, 'EXC-001', 'Excavator 20 Ton', 'Excavator pengeruk tanah', 'Unit', 1500000000.00, 2, 1, '../home/images/alat/excavator20ton.webp', 'active'),
(12, 'EXC-002', 'Mini Excavator 5 Ton', 'Excavator mini lincah', 'Unit', 650000000.00, 3, 1, '../home/images/alat/excavator5ton.webp', 'active'),
(12, 'EXC-003', 'Excavator 30 Ton', 'Excavator kapasitas besar', 'Unit', 2200000000.00, 1, 1, '../home/images/alat/excavator30ton.webp', 'active'),
(13, 'CRN-001', 'Mobile Crane 25 Ton', 'Derek beroda fleksibel', 'Unit', 1800000000.00, 2, 1, '../home/images/alat/crane25t.webp', 'active'),
(13, 'CRN-002', 'Crawler Crane 50 Ton', 'Derek roda rantai berat', 'Unit', 3500000000.00, 1, 1, '../home/images/alat/crawlercrane50t.webp', 'active'),
(13, 'CRN-003', 'Tower Crane', 'Derek konstruksi gedung tinggi', 'Unit', 2800000000.00, 1, 1, '../home/images/alat/towercrane.webp', 'active'),
(14, 'BLD-001', 'Bulldozer D6', 'Bulldozer pendorong tanah', 'Unit', 1600000000.00, 2, 1, '../home/images/alat/bulldozed6.webp', 'active'),
(14, 'BLD-002', 'Bulldozer D8', 'Bulldozer tenaga tinggi', 'Unit', 2400000000.00, 1, 1, '../home/images/alat/bulldozed8.webp', 'active'),
(15, 'WHL-001', 'Wheel Loader 2.5 m³', 'Wheel loader pemuat material', 'Unit', 950000000.00, 2, 1, '../home/images/alat/wheelloader2,5m.webp', 'active'),
(15, 'WHL-002', 'Wheel Loader 3 m³', 'Wheel loader daya angkut besar', 'Unit', 1250000000.00, 2, 1, '../home/images/alat/wheelloader3.webp', 'active'),
(16, 'FLT-001', 'Forklift 3 Ton', 'Forklift gudang 3 ton', 'Unit', 250000000.00, 4, 1, '../home/images/alat/forklift3ton.webp', 'active'),
(16, 'FLT-002', 'Forklift 5 Ton', 'Forklift diesel 5 ton', 'Unit', 380000000.00, 2, 1, '../home/images/alat/forklift5ton.webp', 'active'),
(17, 'DMP-001', 'Dump Truck 10 Ton', 'Truk jungkit pengangkut material', 'Unit', 450000000.00, 5, 1, '../home/images/alat/dumptruck10t.webp', 'active'),
(17, 'DMP-002', 'Dump Truck 20 Ton', 'Truk jungkit heavy duty', 'Unit', 750000000.00, 3, 1, '../home/images/alat/dumptruck20t.webp', 'active'),
(18, 'MIX-001', 'Concrete Mixer 0.5 m³', 'Molen pengaduk semen beton', 'Unit', 45000000.00, 6, 2, '../home/images/alat/concratemix0,5.webp', 'active'),
(19, 'CMP-001', 'Vibro Roller 10 Ton', 'Penggilas getar pemadat tanah', 'Unit', 680000000.00, 2, 1, '../home/images/alat/vibroroller.webp', 'active'),
(20, 'GRD-001', 'Motor Grader', 'Perata tanah jalan raya', 'Unit', 1350000000.00, 1, 1, '../home/images/alat/motorgrader.webp', 'active'),
(21, 'ASP-001', 'Asphalt Paver', 'Mesin penghampar aspal', 'Unit', 1900000000.00, 1, 1, '../home/images/alat/asphaltpaver.webp', 'active'),
(22, 'PUM-001', 'Concrete Pump Truck', 'Truk pompa cor beton cair', 'Unit', 1450000000.00, 1, 1, '../home/images/alat/concrate.webp', 'active'),
(23, 'GEN-001', 'Genset 50 KVA', 'Generator listrik darurat 50 kva', 'Unit', 110000000.00, 3, 1, '../home/images/alat/genset50kva.webp', 'active'),
(23, 'GEN-002', 'Genset 100 KVA', 'Generator listrik industri 100 kva', 'Unit', 1950000000.00, 2, 1, '../home/images/alat/genset100kva.webp', 'active');

UPDATE products p JOIN categories c ON p.category_id = c.id SET p.category = c.name WHERE p.category = '';
