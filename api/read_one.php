<?php

// Membuat Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
  
require_once '../controller/food.php';

use Controller\Food;

// Inisialisasi Food Controller
$foodController = new Food();

// Mengambil Nilai QR Code dari Paramater URL
$qr_code = isset($_GET['qr_code']) ? $_GET['qr_code'] : die();

// Memanggil Fungsi readOne Untuk Mencari Data
$foodController->getData($qr_code);

?>