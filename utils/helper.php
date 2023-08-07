<?php 
namespace Helper {
    // Fungsi Utility
    // Fungsi Untuk Mengirim Status Code dan Data JSON
    function sendJSON($statusCode, $data) {
        // Mengatur Response Code
        http_response_code($statusCode);

        // Menampilkan Pesan dalam Bentuk JSON
        echo json_encode($data);
    }
}
?>