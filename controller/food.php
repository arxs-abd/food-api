<?php 
namespace Controller;

// Memasukkan File Database dan Model Food
require_once '../config/database.php';
require_once '../model/food.php';
require_once '../utils/helper.php';

use Config\Database;
use Model\Food as FoodModel;
use function Helper\sendJSON;
use PDO;


class Food {

    private $db = null;
    protected $food = null;

    function __construct()
    {
        // Inisialisasi Database dan Koneksi
        $database = new Database();
        $this->db = $database->getConnection();
        $this->food = new FoodModel($this->db);
    }

    function getDatas($page = null) {
        $stmt = null;

        // Memanggil Fungsi read pada Model Food
        if ($page) $stmt = $this->food->readWithPaging($page);
        else $stmt = $this->food->read();

        $num = $stmt->rowCount();
        
        // Jika Terdapat Data pada Tabel Food
        if ($num > 0) {
        
            // Inisialisasi Array Untuk Mendapatkan Data Food
            $allFoods = [];
        
            // Melakukan Perulangan Untuk Mendapatkan Semua Data Food
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $isExpired = false;

                extract($row);
                $foodX = new FoodModel($this->db);
                $foodX->id = $id;
                $foodX->expired = $expired;
                $foodX->is_discount = $is_discount;

                // Cek Apakah Sudah Mendapatkan Diskon
                if (!$is_discount) {
                    // Memanggil Fungsi isDiscount 
                    // Untuk Mengecek Apakah Sudah Mendapatkan Diskon Sesuai Jangka Waktu
                    $price = $foodX->isDiscount($price);
                }

                // Cek Apakah Sudah Expire
                if (strtotime('+0 days', time()) > strtotime($foodX->expired)) {
                    $isExpired = true;
                }

                $result = [
                    "id" => $id,
                    "qr_code" => $qr_code,
                    "name" => $name,
                ];
                $addtional = [];
                // Membuat Array yang Akan Menyimpan Hasil Berdasarkan Expired
                if ($isExpired) $addtional = [
                    "status" => "EXPIRED",
                ];
                else $addtional = [
                    "price" => $price,
                    "expired" => $expired,
                ];

                // Memasukkan Satu Row Kedalam Array allFood
                array_push($allFoods, array_merge($result, $addtional));
            }
            $result = [];
            if ($page) $result["halaman"] = $page;
            $result["data"] = $allFoods;
        
            // Mengatur Response Code - 200 OK dan Mengirim Data
            return sendJSON(200, $result);
        }

        // Jika Tidak Terdapat Data pada Tabel Food
        // Mengatur Response Code - 404 Not found dan Mengirim Data
        return sendJSON(404, [
            "pesan" => "Food Tidak Ditemukan",
        ]);
    }

    function getData($qr_code) {
        // Memanggil Fungsi readOne
        $this->food->readOne($qr_code);

        // Melakukan Pengecekan Terhadap Hasil yang Di Dapatkan
        if ($this->food->name != null) {

            // Melakukan Pengecekan Apakah Sudah Mendapatkan Diskon Atau Tidak
            $this->food->isDiscount($this->food->price);

            // Cek Apakah Sudah Expire
            if (strtotime('+0 days', time()) > strtotime($this->food->expired)) {
                $result = [
                    "id" => $this->food->id,
                    "name" => $this->food->name,
                    "status" => "Expired",
                ];

                // Mengatur Response Code - 200 OK dan Mengirim Data
                return sendJSON(200, [
                    "data" => $result,
                ]);
            }

            // Membuat Array yang Akan Menyimpan Hasil Pencarian
            $result = [
                "id" =>  $this->food->id,
                "name" => $this->food->name,
                "qr_code" => $this->food->qr_code,
                "price" => $this->food->price,
                "expired" => $this->food->expired,
            ];
        
            // Mengatur Response Code - 200 OK dan Mengirim Data
            return sendJSON(200, [
                "data" => $result,
            ]);
        }
        
        // Jika Tidak Terdapat Hasil Yang Ditemukan
        // Mengatur Response Code - 404 Not found dan Mengirim Data
        return sendJSON(404, [
            "pesan" => "Food Tidak Ditemukan.",
        ]);
    }

    function create($data) {
        // Melakukan Pengecekan Data yang Dikirim Harus Lengkap
        if (
            !empty($data->qr_code) &&
            !empty($data->name) &&
            !empty($data->price) &&
            !empty($data->expired)
        ) {
            // Set Nilai dari Objek Food
            $this->food->name = $data->name;
            $this->food->qr_code = $data->qr_code;
            $this->food->is_delete = false;
            $this->food->is_discount = false;
            $this->food->expired = $data->expired;

            // Set Price Jika Mendapatkan Discount 10%
            $this->food->price = $this->food->isDiscount($data->price, true);

            // Memanggil Fungsi Untuk Menambahkan Data Baru Pada Tabel Food 
            if ($this->food->create()) {
        
                // Mengatur Response Code - 201 created dan Mengirim Data
                return sendJSON(201, [
                    "pesan" => "Food Berhasil Ditambahkan",
                ]);
            }
        
            // Jika Gagal Melakukan Penambahan Data
            // Mengatur Response Code - 503 service unavailable
            return sendJSON(503, [
                "pesan" => "Tidak Dapat Menambahkan Food Baru.",
            ]);
        }
        
        // Jika Data yang Dikirimkan Tidak Lengkap
        // Mengatur Response Code - 400 bad request
        return sendJSON(400, [
            "pesan" => "Tidak Dapat Menambahkan Food Baru. Data Tidak Lengkap.",
        ]);
    }

    function delete($qr_code) {
        // Memanggil Fungsi softDelete Untuk Menghapus Data Food
        if ($this->food->softDelete($qr_code)){
        
            // Mengatur Response Code - 200 OK dana Mengirim Data
            return sendJSON(200, [
                "pesan" => "Food Berhasil Dihapus."
            ]);
        }
        
        // Mengatur Response Code - 503 service unavailable dan Mengirim Data
        return sendJSON(503, [
            "pesan" => "Tidak Dapat Menghapus Food."
        ]);
    }

    function migrate() {
        $this->food->createTabel();
        sendJSON(201, [
            "pesan" => "Tabel Foods Berhasil Ditambahkan",
        ]);
    }
}
?>