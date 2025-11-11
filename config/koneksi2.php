<?php
// Konfigurasi Database
$host = "localhost";
$user = "root"; 
$password = ""; 
$database = "spmb_alfalah"; 

// Koneksi menggunakan MySQLi
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8");
?>