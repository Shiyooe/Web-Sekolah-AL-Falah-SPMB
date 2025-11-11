<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once 'koneksi.php';

    // ambil data from POST 
    $gmail = $_POST['gmail'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Debug log
    error_log("Received registration data - Gmail: $gmail, Username: $username");

    // wajib di isi gak boleh empty(kosong) 
    if (empty($gmail) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi.']);
        exit;
    }

    // login pake email yg valid 
    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid.']);
        exit;
    }

    // mengambil dari sql database 
    $stmt = $pdo->prepare("SELECT id FROM users WHERE gmail = ? OR username = ?");
    $stmt->execute([$gmail, $username]);
    $existing = $stmt->fetch();

    //jika akun udh ter daftar 
    if ($existing) {
        echo json_encode(['success' => false, 'message' => 'Email atau username sudah terdaftar.']);
        exit;
    }

    // otomatis terisi ke database 
    $stmt = $pdo->prepare("INSERT INTO users (username, gmail, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $gmail, $password])) {
        echo json_encode([
            'success' => true, 
            'message' => 'Pendaftaran berhasil! Silakan login.'
        ]);
        error_log("Registration successful for user: $username");
    } else {
        throw new Exception("Failed to insert data into database");
    }

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
    ]);
}

?>