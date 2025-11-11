<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Jika belum login, redirect ke halaman login
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'redirect' => 'login.php',
        'message' => 'Please login first'
    ]);
    exit;
}

// Jika sudah login, kembalikan data admin
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'admin' => [
        'name' => $_SESSION['admin_name'] ?? '',
        'email' => $_SESSION['admin_email'] ?? ''
    ]
]);
?>