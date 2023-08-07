<?php
namespace Api\Delete;

// Membuat Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Memasukkan File Database dan Model Food
require_once '../config/database.php';
require_once '../model/food.php';

use Config\Database;
use Model\Food;
  
// Inisialisasi Database dan Koneksi
$database = new Database();
$db = $database->getConnection();
  
// Inisialisasi Objek Food
$food = new Food($db);
  
// Mengambil Data Dari Body POST
$data = json_decode(file_get_contents("php://input"));
  
// Memanggil Fungsi softDelete Untuk Menghapus Data Food
if ($food->softDelete($data->qr_code)){
  
    // Mengatur Response Code - 200 OK
    http_response_code(200);

    // Menampilkan Hasil dalam Bentuk JSON
    echo json_encode([
        "pesan" => "Food Berhasil Dihapus."
    ]);
    return;
}
  
// Mengatur Response Code - 503 service unavailable
http_response_code(503);

// Menampilkan Hasil dalam Bentuk JSON
echo json_encode([
    "pesan" => "Tidak Dapat Menghapus Food."
]);
return;
?>