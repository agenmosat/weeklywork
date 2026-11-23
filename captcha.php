<?php
require_once __DIR__ . '/includes/session.php';

$chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
$code = '';
for ($i = 0; $i < 5; $i++) {
    $code .= $chars[rand(0, strlen($chars) - 1)];
}

$_SESSION['captchacode'] = $code;

header('Content-Type: image/png');
header('Cache-Control: no-cache, no-store, must-revalidate');

$clean = escapeshellarg($code);
$cmd = "magick -size 140x44 xc:\"#eef2f5\" -stroke \"#b0c0d0\" -strokewidth 1 -draw \"line 10,20 130,25 line 15,35 125,10\" -stroke none -fill \"#1a2a3a\" -pointsize 26 -font DejaVu-Sans-Bold -draw \"text 22,32 $clean\" -wave 2x40 -swirl 15 png:-";
$png = shell_exec($cmd);

if ($png) {
    echo $png;
} else {
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="140" height="44"><rect width="140" height="44" fill="#eef2f5"/><text x="25" y="30" font-family="sans-serif" font-size="24" font-weight="bold" fill="#1a2a3a">' . htmlspecialchars($code) . '</text></svg>';
}
exit();
