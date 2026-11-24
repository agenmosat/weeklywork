<?php
require_once __DIR__ . '/../home/includes/session.php';
if (!isset($_SESSION['user'])) {
    $target = file_exists('index.php') ? 'index.php' : (file_exists('../index.php') ? '../index.php' : '../../index.php');
    header('Location: ' . $target . '?login=1');
    exit;
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    $target = file_exists('home/dashboard.php') ? 'home/dashboard.php' : (file_exists('../home/dashboard.php') ? '../home/dashboard.php' : '../../home/dashboard.php');
    header('Location: ' . $target);
    exit;
}
