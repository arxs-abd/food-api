<?php
namespace Model;
use PDO;

class Food {
  
    // Koneksi Ke Database
    private $conn;
    private $table_name = "foods";
  
    // Properti Pada Tabel foods
    public $id;
    public $qr_code;
    public $name;
    public $expired;
    public $price;
    public $is_delete;
    public $is_discount;
  
    // Constructor dengan paramater koneksi Database
    public function __construct($db){
        $this->conn = $db;
    }

    // Fungsi untuk mendapatkan semua data pada tabel Food
    function read() {
    
        // Query SQL Untuk Mendapatkan Semua Data
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_delete = 0";
        $stmt = $this->conn->prepare($query);
    
        // Menjalankan Query
        $stmt->execute();

        return $stmt;
    }

    // Fungsi Untuk Menambahkan Data Baru Pada Tabel Food
    function create() {
    
        // Query SQL Untuk Menambahkan Data Baru
        $query = "INSERT INTO
                    " . $this->table_name . "
                SET
                    qr_code=:qr_code, name=:name, price=:price, expired=:expired, is_delete=:is_delete, is_discount=:is_discount";
        $stmt = $this->conn->prepare($query);
    
        // Melakukan Sanitizing Pada Data
        $this->qr_code = htmlspecialchars(strip_tags($this->qr_code));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->expired = htmlspecialchars(strip_tags($this->expired));
        $this->is_delete = htmlspecialchars(strip_tags($this->is_delete));
        $this->is_discount = htmlspecialchars(strip_tags($this->is_discount));
    
        // Melakukan Binding dari Statment Query Sebelumnya
        $stmt->bindParam(":qr_code", $this->qr_code);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":expired", $this->expired);
        $stmt->bindParam(":is_delete", $this->is_delete);
        $stmt->bindParam(":is_discount", $this->is_discount);
    
        // Menjalankan Query
        if($stmt->execute()) return true;
        return false;
        
    }

    // Fungsi Untuk Mendapatkan Salah Satu Tabel Food berdasarkan QR Code
    function readOne($qr_code) {
    
        // Query SQL Untuk Mendapatkan Salah Satu Data Food Berdasarkan QR Code
        $query = "SELECT * FROM " . $this->table_name . " WHERE qr_code = :qr_code AND is_delete = 0";
        $stmt = $this->conn->prepare($query);
    
        // Melakukan Sanitizing dan Binding dari Statment Query Sebelumnya
        $qr_code = htmlspecialchars(strip_tags($qr_code));
        $stmt->bindParam(":qr_code", $qr_code);
    
        // Menjalankan Query
        $stmt->execute();
    
        // Mendapatkan Hasil Query
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Melakukan Pengecekan Terhadap Hasil Query, 
        // Jika Tidak Ada Hasil, Maka Akan Melakukan Return
        if (!$row) return;
    
        // Memasukkan Hasil Query ke Dalam Objek Food
        $this->id = $row['id'];
        $this->qr_code = $row['qr_code'];
        $this->name = $row['name'];
        $this->price = $row['price'];
        $this->expired = $row['expired'];
        $this->is_delete = $row['is_delete'];
        $this->is_discount = $row['is_discount'];
    }

    // Fungsi Untuk Menghapus Data Dari Tabel Food
    function softDelete($qr_code) {

        // Query SQL Untuk Meperbaharui Field isDelete pada Tabel Food
        $query = "UPDATE " . $this->table_name . " SET is_delete = 1 WHERE qr_code = :qr_code";
        $stmt = $this->conn->prepare($query);

        // Melakukan Sanitizing Binding dari Statment Query Sebelumnya
        $qr_code = htmlspecialchars(strip_tags($qr_code));
        $stmt->bindParam(":qr_code", $qr_code);

        // Menjalankan Query
        if($stmt->execute()) return true;
        return false;
    }

    // Fungsi Untuk Melakukan Paging
    function readWithPaging($page) {
        $itemPerPage = 5;
        $min = ($page > 1) ? ($itemPerPage * ($page - 1)) : 0;
        $max = $itemPerPage * $page;

        // Query SQL Untuk Melakukan Paging Pada Data Food
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_delete = 0 " . " LIMIT " . $min . ", " . $max;
        $stmt = $this->conn->prepare($query);

        // Menjalankan Query
        $stmt->execute();
        return $stmt;
    }


    // Fungsi Untuk Mengupdate Harga Diskon dan is_disocunt
    function updateDiscount($price) {
        // Query SQL Untuk Melakukan Update Telah Di Diskon
        $query = "UPDATE " . $this->table_name . " SET is_discount = 1, price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Melakukan Binding dari Statemant Query Sebelumnya
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":id", $this->id);

        // Menjalankan Query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // Fungsi Utility
    // Fungsi Untuk Mendapatkan Harga Diskon Atau Tidak
    function isDiscount($price, $create = false) {
        // Inisialisasi Waktu Sekarang, Waktu Expired, dan 10 Hari Kedepan
        $now = time();
        $timeExpire = strtotime($this->expired);
        $tenDay = strtotime('+10 days', $now);

        // Melakukan Pengecekan Terhadap Waktu Expired Sudah Kurang dari 10 Hari
        if ($timeExpire < $tenDay && !$this->is_discount) {
            $newPrice = $price * 0.9;
            if (!$create) {
                $this->updateDiscount($newPrice);
                $this->price = $newPrice;
                return $newPrice;
            }
            $this->is_discount = 1;
            return $newPrice;
        }

        // Jika Belum, Maka Harga Tidak Berubah
        return $price;
    }

    function createTabel() {
        $query = "CREATE TABLE foods (
            id int(11) NOT NULL,
            qr_code varchar(20) NOT NULL,
            name varchar(20) NOT NULL,
            expired varchar(20) NOT NULL,
            price int(11) NOT NULL,
            is_delete tinyint(1) NOT NULL,
            is_discount tinyint(1) NOT NULL
          )";
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
    }
}
?>