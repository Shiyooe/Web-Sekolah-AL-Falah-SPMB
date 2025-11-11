<?php
session_start();

// Cek login
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pembayaran = mysqli_real_escape_string($conn, $_POST['id_pembayaran']);
    
    // Cek apakah ada file yang diupload
    if (!isset($_FILES['bukti_files']) || empty($_FILES['bukti_files']['name'][0])) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
        exit();
    }
    
    // Unified path dengan prosesbayar.php
    $upload_dir = '../uploads/bukti/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $uploaded_files = [];
    $total_files = count($_FILES['bukti_files']['name']);
    
    // Loop untuk setiap file
    for ($i = 0; $i < $total_files; $i++) {
        $file_name = $_FILES['bukti_files']['name'][$i];
        $file_tmp = $_FILES['bukti_files']['tmp_name'][$i];
        $file_size = $_FILES['bukti_files']['size'][$i];
        $file_error = $_FILES['bukti_files']['error'][$i];
        
        // Skip jika ada error
        if ($file_error !== UPLOAD_ERR_OK) {
            continue;
        }
        
        // Validasi ukuran file (max 2MB)
        if ($file_size > 2097152) {
            echo json_encode(['success' => false, 'message' => 'File terlalu besar (max 2MB)']);
            exit();
        }
        
        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = mime_content_type($file_tmp);
        
        if (!in_array($file_type, $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Format file tidak valid. Gunakan JPG, JPEG, atau PNG']);
            exit();
        }
        
        // Generate nama file unik
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = 'bukti_' . $id_pembayaran . '_' . time() . '_' . $i . '.' . $file_ext;
        
        // Upload file
        if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
            $uploaded_files[] = $new_file_name;
        }
    }
    
    if (empty($uploaded_files)) {
        echo json_encode(['success' => false, 'message' => 'Gagal upload file']);
        exit();
    }
    
    // Get existing bukti pembayaran
    $query_check = "SELECT bukti_pembayaran FROM pembayaran WHERE id_pembayaran = '$id_pembayaran'";
    $result_check = mysqli_query($conn, $query_check);
    $existing_bukti = mysqli_fetch_assoc($result_check)['bukti_pembayaran'];
    
    // Gabungkan dengan bukti yang sudah ada
    if (!empty($existing_bukti)) {
        $all_bukti = $existing_bukti . ',' . implode(',', $uploaded_files);
    } else {
        $all_bukti = implode(',', $uploaded_files);
    }
    
    // Update database
    $query_update = "UPDATE pembayaran SET bukti_pembayaran = '$all_bukti' WHERE id_pembayaran = '$id_pembayaran'";
    
    if (mysqli_query($conn, $query_update)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Bukti pembayaran berhasil diupload',
            'files' => $uploaded_files
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal update database: ' . mysqli_error($conn)]);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>