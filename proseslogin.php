<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    require_once 'koneksi.php';
    session_start();

    // Ambil data dari POST
    $identifier = trim($_POST['identifier'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validasi input kosong
    if (empty($identifier)) {
        echo json_encode(['success' => false, 'message' => 'Email/username wajib diisi!']);
        exit;
    }
    
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Password wajib diisi!']);
        exit;
    }

    // Validasi panjang minimum password
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter!']);
        exit;
    }

    // Login sebagai admin (gunakan password yang lebih aman di production)
    if ($identifier === 'lf.3!]hSu+^LS@u!Bj4!~^atcjDNIY') {
        if ($password === 'lf.3!]hSu+^LS@u!Bj4!~^atcjDNIY') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['username'] = 'lf.3!]hSu+^LS@u!Bj4!~^atcjDNIY';
            $_SESSION['role'] = 'admin';
            echo json_encode(['success' => true, 'redirect' => './admin/login.php']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Password admin salah!']);
            exit;
        }
    }

    // Query untuk mencari user berdasarkan username ATAU gmail
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR gmail = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Akun tidak ditemukan! Silakan periksa username/email Anda.']);
        exit;
    }

    // Verifikasi password
    if ($user['password'] !== $password) {
        echo json_encode(['success' => false, 'message' => 'Password yang Anda masukkan salah!']);
        exit;
    }

    // Login berhasil - simpan session user
    $_SESSION['user_logged_in'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['gmail'] = $user['gmail'];
    $_SESSION['role'] = 'user';

    // Kirim response sukses
    echo json_encode(['success' => true, 'redirect' => 'brosur/index.html']);
    exit;

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
    exit;
}
?>