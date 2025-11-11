<?php
// Konfigurasi koneksi database
$host = 'localhost';
$dbname = 'spmb_alfalah';
$username = 'root';
$password = '';

// Koneksi menggunakan MySQLi (untuk bayar.php)
$conn = mysqli_connect($host, $username, $password, $dbname);

// Cek koneksi MySQLi
if (!$conn) {
    die("Koneksi MySQLi gagal: " . mysqli_connect_error());
}

// Set charset untuk menghindari masalah encoding
mysqli_set_charset($conn, "utf8mb4");

// Koneksi menggunakan PDO (untuk prosesbayar.php)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi PDO gagal: " . $e->getMessage());
}
?>