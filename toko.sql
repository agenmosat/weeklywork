CREATE DATABASE IF NOT EXISTS toko;
USE toko;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'images/glock17.png'
);

CREATE TABLE IF NOT EXISTS kasir (
    id_kasir INT AUTO_INCREMENT PRIMARY KEY,
    nama_kasir VARCHAR(50) NOT NULL,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    tanggal_keluar DATE DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS supplier (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(50) NOT NULL,
    kota VARCHAR(30) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_pesanan VARCHAR(50) NOT NULL,
    nama_pembeli VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    status_pembayaran VARCHAR(50) NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, price, stock, category, description, image)
SELECT seed.name, seed.price, seed.stock, seed.category, seed.description, seed.image
FROM (
    SELECT 'glock 19 gen 5' AS name, 520.00 AS price, 14 AS stock, 'weapons' AS category, 'compact 9mm semi automatic pistol with stripped serial numbers and two 15 round magazines.' AS description, 'images/glock17.png' AS image
    UNION ALL SELECT 'ak47 assault rifle', 950.00, 8, 'weapons', '7.62x39mm automatic rifle with folding stock, threaded muzzle and two 30 round magazines.', 'images/ak47.jpg'
    UNION ALL SELECT 'colt m4a1 carbine', 1300.00, 5, 'weapons', '5.56x45mm military grade carbine with picatinny rail system and unmarked receiver.', 'images/coltm4.jpg'
    UNION ALL SELECT 'remington 870 shotgun', 460.00, 12, 'weapons', '12 gauge pump action tactical shotgun with short barrel and heat shield.', 'images/remington870.jpg'
    UNION ALL SELECT 'mp5 submachine gun', 1400.00, 4, 'weapons', 'compact 9mm submachine gun with retractable stock and threaded barrel for suppressor.', 'images/mp5mchine.jpg'
    UNION ALL SELECT 'desert eagle 50ae', 1650.00, 3, 'weapons', 'heavy .50 ae semi automatic handgun with matte black tactical coat and clean barrel.', 'images/deserteagle50.jpg'
    UNION ALL SELECT 'sig sauer p320', 580.00, 10, 'weapons', 'modular 9mm combat pistol with night sights and ambidextrous slide lock.', 'images/sigsauer.jpg'
    UNION ALL SELECT 'tactical combat knife', 75.00, 20, 'weapons', 'full tang blackened steel combat knife with serrated spine and kydex sheath.', 'images/tusuk.jpg'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.name = seed.name);

INSERT INTO kasir (nama_kasir, status)
SELECT seed.nama_kasir, seed.status
FROM (
    SELECT 'kasir1' AS nama_kasir, 'aktif' AS status
    UNION ALL SELECT 'kasir2', 'nonaktif'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM kasir k WHERE k.nama_kasir = seed.nama_kasir);

INSERT INTO supplier (nama_supplier, kota)
SELECT seed.nama_supplier, seed.kota
FROM (
    SELECT 'supplier1' AS nama_supplier, 'jakarta' AS kota
    UNION ALL SELECT 'supplier2', 'bandung'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM supplier s WHERE s.nama_supplier = seed.nama_supplier);

DELETE p1 FROM products p1
INNER JOIN products p2
WHERE p1.name = p2.name AND p1.id > p2.id;
