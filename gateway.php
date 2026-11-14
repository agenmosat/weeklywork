<?php
function processgatewaypayment($orderid, $amount, $method, $customer) {
    if ($amount <= 0) {
        return [
            "success" => false,
            "message" => "invalid payment amount"
        ];
    }

    $validmethods = ["crypto escrow", "credit card", "bank transfer", "cash on delivery"];
    if (!in_array($method, $validmethods)) {
        return [
            "success" => false,
            "message" => "unsupported payment method"
        ];
    }

    $txnid = "TXN" . strtoupper(bin2hex(random_bytes(6)));
    $escrowhash = hash("sha256", $orderid . $amount . time());

    return [
        "success" => true,
        "transaction_id" => $txnid,
        "escrow_hash" => substr($escrowhash, 0, 32),
        "gateway" => "Darkfonder Escrow Gateway API v1",
        "timestamp" => date("Y-m-d H:i:s"),
        "method" => $method,
        "amount" => $amount
    ];
}
