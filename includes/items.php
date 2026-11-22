<?php
require_once __DIR__ . '/../config/database.php';

if (!function_exists('urlen')) {
    function urlen($id) {
        $cipher = openssl_encrypt((string)$id, 'AES-128-ECB', 'k4sirqu_secret_key');
        return rtrim(strtr($cipher, '+/', '-_'), '=');
    }
}

if (!function_exists('urlde')) {
    function urlde($param) {
        if (empty($param)) {
            return 0;
        }
        $cipher = strtr((string)$param, '-_', '+/');
        $plain = openssl_decrypt($cipher, 'AES-128-ECB', 'k4sirqu_secret_key');
        if ($plain !== false && is_numeric($plain)) {
            return (int)$plain;
        }
        if (is_numeric($param)) {
            return (int)$param;
        }
        return 0;
    }
}

if (!function_exists('samarkan_id')) {
    function samarkan_id($id) {
        return urlen($id);
    }
}

if (!function_exists('uraikan_id')) {
    function uraikan_id($param) {
        return urlde($param);
    }
}

$products = [];

$result = mysqli_query($koneksi, "SELECT * FROM products ORDER BY id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id = (int) $row['id'];
        $img = !empty($row['image']) ? $row['image'] : "images/no-image.webp";
        $products[$id] = [
            "id" => $id,
            "name" => $row['name'],
            "price" => (float) $row['price'],
            "stock" => (int) $row['stock'],
            "unit" => !empty($row['unit']) ? $row['unit'] : "buah",
            "category" => $row['category'],
            "image" => $img,
            "desc" => $row['description'],
        ];
    }
}
?>
