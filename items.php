<?php
require_once __DIR__ . '/config/database.php';

$products = [];

$result = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['id'];
        $img = !empty($row['image']) ? $row['image'] : "images/glock17.png";
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
