<?php
// config.php

// Definisikan konstanta untuk koneksi database
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Jangan lupa ganti password MySQL kamu kalo ada
define('DB_NAME', 'spmb_alfalah');

/**
 * Fungsi untuk membuat koneksi ke database
 * @return mysqli|false Mengembalikan objek koneksi atau false jika gagal
 */
function getDBConnection() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Cek koneksi
    if ($conn->connect_error) {
        // Kita return false aja, nanti di file yang pake bisa dicek
        return false;
        // Atau, kamu bisa pake die() kalo mau langsung berhenti
        // die("Koneksi database gagal: " . $conn->connect_error);
    }

    return $conn;
}
?>