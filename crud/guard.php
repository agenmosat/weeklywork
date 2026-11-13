<?php
require_once __DIR__ . '/../session.php';
if (!isset($_SESSION['user']) || (($_SESSION['role'] ?? '') !== 'admin' && ($_SESSION['role'] ?? '') !== 'cashier')) {
    header('Location: ../../index.php');
    exit;
}
