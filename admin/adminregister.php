<?php
session_start();
require_once("../config/koneksi.php");

// Set header JSON
header('Content-Type: application/json');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Validasi input
    if (empty($first_name)) {
        $errors[] = "Nama depan wajib diisi.";
    }
    
    if (empty($last_name)) {
        $errors[] = "Nama belakang wajib diisi.";
    }
    
    if (empty($email)) {
        $errors[] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    
    if (empty($password)) {
        $errors[] = "Password wajib diisi.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok.";
    }
    
    // Jika ada errors dari validasi
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'errors' => $errors
        ]);
        exit;
    }
    
    // Jika tidak ada error, lanjutkan ke database
    try {
        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare("SELECT id_admin FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'errors' => ["Email sudah terdaftar."]
            ]);
            exit;
        }
        
        // Simpan ke database TANPA hashing (sesuai permintaan)
        $stmt = $pdo->prepare("INSERT INTO admin (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$first_name, $last_name, $email, $password])) {
            echo json_encode([
                'success' => true,
                'message' => 'Registrasi berhasil! Silakan login.'
            ]);
            exit;
        } else {
            throw new Exception("Gagal menyimpan data");
        }
        
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'errors' => ["Terjadi kesalahan database. Silakan coba lagi."]
        ]);
        exit;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'errors' => ["Terjadi kesalahan. Silakan coba lagi."]
        ]);
        exit;
    }
    
} else {
    echo json_encode([
        'success' => false,
        'errors' => ['Metode request tidak valid']
    ]);
    exit;
}
?>