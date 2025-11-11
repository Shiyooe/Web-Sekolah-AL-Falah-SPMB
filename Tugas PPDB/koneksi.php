<?php

$host = 'localhost'; 
$dbname = 'spmb_alfalah'; // Database aku 
$username_db = 'root';
$password_db = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Test koneksi
    $pdo->query("SELECT 1");
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal. Silakan coba lagi nanti."
    ]);
    exit;
}
?>