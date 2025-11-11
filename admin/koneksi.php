<?php
// Konfigurasi koneksi database
$host = "localhost"; 
$user = "root";      
$password = "";     
$database = "spmb_alfalah"; 

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
