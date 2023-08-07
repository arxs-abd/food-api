<?php
namespace Config;

use PDO;
use PDOException;

class Database {
  
    // Database Connection
    private $host = "localhost";
    private $db_name = "tes";
    private $username = "root";
    private $password = "";
    public $conn;
  
    // Mendapatkan Koneksi Ke Database
    public function getConnection(){
  
        $this->conn = null;
  
        try {
            // Inisialisasi Objek PDO
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            
        } catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>