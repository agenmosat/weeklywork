<?php
require_once __DIR__ . '/config/database.php';

$products = [];
$images = [
    "glock 19 gen 5" => "images/glock17.png",
    "ak47 assault rifle" => "images/ak47.jpg",
    "colt m4a1 carbine" => "images/coltm4.jpg",
    "remington 870 shotgun" => "images/remington870.jpg",
    "mp5 submachine gun" => "images/mp5mchine.jpg",
    "desert eagle 50ae" => "images/deserteagle50.jpg",
    "sig sauer p320" => "images/sigsauer.jpg",
    "tactical combat knife" => "images/tusuk.jpg",
];

$result = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['id'];
        $name = strtolower(trim($row['name']));
        $img = !empty($row['image']) ? $row['image'] : ($images[$name] ?? "images/glock17.png");
        $products[$id] = [
            "id" => $id,
            "name" => $row['name'],
            "price" => (float) $row['price'],
            "stock" => (int) $row['stock'],
            "category" => $row['category'],
            "image" => $img,
            "desc" => $row['description'],
        ];
    }
}
?>
