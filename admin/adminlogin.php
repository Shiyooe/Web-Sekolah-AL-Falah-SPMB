<?php
ob_clean();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
require_once("../config/koneksi.php");

error_log("Login attempt - Email: " . ($_POST['email'] ?? 'not set') . ", Password length: " . (isset($_POST['password']) ? strlen($_POST['password']) : 0));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    error_log("Processing login - Email: $email");

    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("Query result - Admin found: " . ($admin ? "Yes" : "No"));

    if (!$admin) {
        throw new Exception('Email not found');
    }

    if ($password !== $admin['password']) {
        error_log("Password mismatch - Input: $password, Stored: {$admin['password']}");
        throw new Exception('Invalid password');
    }

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin['id_admin'];
    $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
    $_SESSION['admin_email'] = $admin['email'];

    ob_clean();
        $response = [
        'success' => true,
        'redirect' => 'index.php', 
        'message' => 'Login successful'
    ];
    
    echo json_encode($response);
    exit;

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    ob_clean();
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    
    echo json_encode($response);
    exit;
}
?>
