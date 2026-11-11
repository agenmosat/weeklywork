<?php
mysqli_report(MYSQLI_REPORT_OFF);

const DB_HOST = '127.0.0.1';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'toko';

$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
