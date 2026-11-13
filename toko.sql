CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT NOT NULL
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

INSERT INTO products (name, price, stock, category, description) VALUES
('glock 19 gen 5', 520.00, 14, 'weapons', 'compact 9mm semi automatic pistol with stripped serial numbers and two 15 round magazines.'),
('ak47 assault rifle', 950.00, 8, 'weapons', '7.62x39mm automatic rifle with folding stock, threaded muzzle and two 30 round magazines.'),
('colt m4a1 carbine', 1300.00, 5, 'weapons', '5.56x45mm military grade carbine with picatinny rail system and unmarked receiver.'),
('remington 870 shotgun', 460.00, 12, 'weapons', '12 gauge pump action tactical shotgun with short barrel and heat shield.'),
('mp5 submachine gun', 1400.00, 4, 'weapons', 'compact 9mm submachine gun with retractable stock and threaded barrel for suppressor.'),
('desert eagle 50ae', 1650.00, 3, 'weapons', 'heavy .50 ae semi automatic handgun with matte black tactical coat and clean barrel.'),
('sig sauer p320', 580.00, 10, 'weapons', 'modular 9mm combat pistol with night sights and ambidextrous slide lock.'),
('tactical combat knife', 75.00, 20, 'weapons', 'full tang blackened steel combat knife with serrated spine and kydex sheath.');

INSERT INTO kasir (nama_kasir, status) VALUES
('kasir1', 'aktif'),
('kasir2', 'nonaktif');

INSERT INTO supplier (nama_supplier, kota) VALUES
('supplier1', 'jakarta'),
('supplier2', 'bandung');
