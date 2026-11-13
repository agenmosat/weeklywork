<?php
require_once __DIR__ . '/config/database.php';

$products = [];
$images = [
    1 => "images/glock17.png",
    2 => "images/ak47.jpg",
    3 => "images/coltm4.jpg",
    4 => "images/remington870.jpg",
    5 => "images/mp5mchine.jpg",
    6 => "images/deserteagle50.jpg",
    7 => "images/sigsauer.jpg",
    8 => "images/tusuk.jpg",
];

$result = mysqli_query($koneksi, "SELECT id, name, price, stock, category, description FROM products ORDER BY id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['id'];
        $products[$id] = [
            "id" => $id,
            "name" => $row['name'],
            "price" => (float) $row['price'],
            "stock" => (int) $row['stock'],
            "category" => $row['category'],
            "image" => $images[$id] ?? "images/glock17.png",
            "desc" => $row['description'],
        ];
    }
}
?>
