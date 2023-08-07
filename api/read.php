<?php
namespace Api\Read;

// Membuat Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../controller/food.php';

use Controller\Food;

// Inisialisasi Food Controller
$foodController = new Food();

// Cek Apakah Terdapat Parameter page Untuk Paging Atau Tidak
$page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : false;
if ($page) {

    // Menjalankan Fungsi getData Untuk Melakukan Paging Berdasarkan Halaman
    return $foodController->getDatas($page);
}

// Menjalankan Fungsi getData Untuk Mendapatkan Semua Data
return $foodController->getDatas();