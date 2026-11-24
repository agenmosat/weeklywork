<?php
require_once __DIR__ . '/../home/includes/gateway.php';

header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    $data = $_POST;
}

$orderid = $data['orderid'] ?? ('onion' . rand(10000, 99999));
$amount = (float)($data['amount'] ?? 0);
$method = $data['method'] ?? 'crypto escrow';
$customer = $data['customer'] ?? 'anonymous';

$result = processgatewaypayment($orderid, $amount, $method, $customer);
echo json_encode($result);
