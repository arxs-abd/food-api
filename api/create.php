<?php
namespace Api\Create;

// Membuat Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../controller/food.php';

use Controller\Food;

// Inisialisasi Food Controller
$foodController = new Food();
  
// Mengambil Data Dari Body POST
$data = json_decode(file_get_contents("php://input"));

// Memanggil Fungsi create Untuk Membuat Data Baru
$foodController->create($data);
?>