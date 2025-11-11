<?php
session_start();

// Konfigurasi database
$host = 'localhost';
$dbname = 'spmb_alfalah';
$username = 'root';
$password = '';

// Koneksi ke database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Cek apakah form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil data dari form
    $nisn = trim($_POST['nisn']);
    $nama = trim($_POST['nama']);
    $tanggal_pembayaran = $_POST['tanggal_pembayaran'];
    $jenis_pembayaran = $_POST['jenis_pembayaran'];
    $jumlah_pembayaran = $_POST['jumlah_asli']; // Nilai tanpa format
    
    // Validasi data wajib
    if (empty($nisn) || empty($nama) || empty($tanggal_pembayaran) || empty($jenis_pembayaran) || empty($jumlah_pembayaran)) {
        $_SESSION['error'] = "Semua field wajib diisi!";
        header("Location: pembayaran.html");
        exit();
    }
    
    // Cek apakah NISN ada di database pendaftaran
    $stmt = $conn->prepare("SELECT id_pendaftaran FROM pendaftaran WHERE nisn = :nisn AND nama = :nama");
    $stmt->execute([':nisn' => $nisn, ':nama' => $nama]);
    $pendaftaran = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pendaftaran) {
        $_SESSION['error'] = "Data pendaftaran tidak ditemukan! Pastikan NISN dan Nama sesuai.";
        header("Location: pembayaran.html");
        exit();
    }
    
    $id_pendaftaran = $pendaftaran['id_pendaftaran'];
    
    // Validasi minimal cicilan
    if ($jenis_pembayaran == 'di_cicil' && $jumlah_pembayaran < 500000) {
        $_SESSION['error'] = "Jumlah cicilan minimal Rp 500.000";
        header("Location: pembayaran.html");
        exit();
    }
    
    // Tentukan status pembayaran
    $biaya_total = 3350000;
    
    // Handle upload file bukti pembayaran
    $bukti_pembayaran = null;
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
        $file = $_FILES['bukti_pembayaran'];
        
        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Format file tidak valid! Hanya JPG, JPEG, dan PNG yang diperbolehkan.";
            header("Location: pembayaran.html");
            exit();
        }
        
        // Validasi ukuran file (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran file terlalu besar! Maksimal 5MB.";
            header("Location: pembayaran.html");
            exit();
        }
        
        // Buat nama file unik
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $timestamp = time();
        $new_filename = 'bukti_' . $id_pendaftaran . '_' . $timestamp . '.' . $file_ext;
        
        // Folder upload
        $upload_dir = '../admin/uploads/bukti_pembayaran/';
        
        // Buat folder jika belum ada
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $bukti_pembayaran = $upload_path;
        } else {
            $_SESSION['error'] = "Gagal mengupload file bukti pembayaran!";
            header("Location: pembayaran.html");
            exit();
        }
    } else {
        $_SESSION['error'] = "Bukti pembayaran wajib diupload!";
        header("Location: pembayaran.html");
        exit();
    }
    
    // Mulai transaksi
    $conn->beginTransaction();
    
    try {
        // Cek apakah sudah ada pembayaran untuk pendaftaran ini
        $stmt = $conn->prepare("SELECT id_pembayaran, jumlah_pembayaran, status_pembayaran, total_cicilan FROM pembayaran WHERE id_pendaftaran = :id_pendaftaran");
        $stmt->execute([':id_pendaftaran' => $id_pendaftaran]);
        $existing_payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing_payment) {
            // Update pembayaran yang ada (untuk cicilan)
            $total_bayar = $existing_payment['jumlah_pembayaran'] + $jumlah_pembayaran;
            $total_cicilan = $existing_payment['total_cicilan'] + 1;
            
            if ($total_bayar >= $biaya_total) {
                $status_pembayaran = 'Lunas';
            } else {
                $status_pembayaran = 'Belum Lunas';
            }
            
            // Update tabel pembayaran
            $stmt = $conn->prepare("UPDATE pembayaran SET 
                Tanggal_pembayaran = :tanggal,
                jumlah_pembayaran = :jumlah,
                status_pembayaran = :status,
                bukti_pembayaran = :bukti,
                total_cicilan = :total_cicilan
                WHERE id_pembayaran = :id");
            
            $stmt->execute([
                ':tanggal' => $tanggal_pembayaran,
                ':jumlah' => $total_bayar,
                ':status' => $status_pembayaran,
                ':bukti' => $bukti_pembayaran,
                ':total_cicilan' => $total_cicilan,
                ':id' => $existing_payment['id_pembayaran']
            ]);
            
            $id_pembayaran = $existing_payment['id_pembayaran'];
            
            // Insert ke riwayat pembayaran
            $stmt = $conn->prepare("INSERT INTO riwayat_pembayaran (id_pembayaran, id_pendaftaran, tanggal_bayar, jumlah_bayar, bukti_bayar, catatan) 
                VALUES (:id_pembayaran, :id_pendaftaran, :tanggal, :jumlah, :bukti, :catatan)");
            
            $catatan = "Cicilan ke-" . $total_cicilan . " (Total: Rp " . number_format($total_bayar, 0, ',', '.') . ")";
            
            $stmt->execute([
                ':id_pembayaran' => $id_pembayaran,
                ':id_pendaftaran' => $id_pendaftaran,
                ':tanggal' => $tanggal_pembayaran,
                ':jumlah' => $jumlah_pembayaran,
                ':bukti' => $bukti_pembayaran,
                ':catatan' => $catatan
            ]);
            
            $_SESSION['success'] = "Pembayaran cicilan berhasil ditambahkan! Total: Rp " . number_format($total_bayar, 0, ',', '.');
            
        } else {
            // Insert pembayaran baru
            if ($jenis_pembayaran == 'bayar_semua' && $jumlah_pembayaran >= $biaya_total) {
                $status_pembayaran = 'Lunas';
            } else {
                $status_pembayaran = 'Belum Lunas';
            }
            
            $stmt = $conn->prepare("INSERT INTO pembayaran (id_pendaftaran, Tanggal_pembayaran, jumlah_pembayaran, status_pembayaran, bukti_pembayaran, total_cicilan) 
                VALUES (:id_pendaftaran, :tanggal, :jumlah, :status, :bukti, 1)");
            
            $stmt->execute([
                ':id_pendaftaran' => $id_pendaftaran,
                ':tanggal' => $tanggal_pembayaran,
                ':jumlah' => $jumlah_pembayaran,
                ':status' => $status_pembayaran,
                ':bukti' => $bukti_pembayaran
            ]);
            
            $id_pembayaran = $conn->lastInsertId();
            
            // Insert ke riwayat pembayaran
            $stmt = $conn->prepare("INSERT INTO riwayat_pembayaran (id_pembayaran, id_pendaftaran, tanggal_bayar, jumlah_bayar, bukti_bayar, catatan) 
                VALUES (:id_pembayaran, :id_pendaftaran, :tanggal, :jumlah, :bukti, :catatan)");
            
            $catatan = $status_pembayaran == 'Lunas' ? 'Pembayaran Lunas' : 'Pembayaran Pertama';
            
            $stmt->execute([
                ':id_pembayaran' => $id_pembayaran,
                ':id_pendaftaran' => $id_pendaftaran,
                ':tanggal' => $tanggal_pembayaran,
                ':jumlah' => $jumlah_pembayaran,
                ':bukti' => $bukti_pembayaran,
                ':catatan' => $catatan
            ]);
            
            $_SESSION['success'] = "Pembayaran berhasil disimpan!";
        }
        
        // Commit transaksi
        $conn->commit();
        
        // Redirect ke halaman selesai
        header("Location: selesai.html");
        exit();
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollBack();
        
        // Hapus file yang sudah diupload jika ada error
        if ($bukti_pembayaran && file_exists($bukti_pembayaran)) {
            unlink($bukti_pembayaran);
        }
        
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
        header("Location: pembayaran.html");
        exit();
    }
    
} else {
    // Jika bukan POST request
    $_SESSION['error'] = "Metode request tidak valid!";
    header("Location: pembayaran.html");
    exit();
}
?>