<?php
header('Content-Type: application/json');

// Koneksi ke database
require_once '../config/koneksi3.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID pembayaran tidak valid']);
    exit();
}

$id_pembayaran = intval($_GET['id']);

// Query untuk mendapatkan riwayat pembayaran
$query = "SELECT 
            r.tanggal_bayar,
            r.jumlah_bayar,
            r.bukti_bayar,
            r.catatan,
            r.created_at
          FROM riwayat_pembayaran r
          WHERE r.id_pembayaran = ?
          ORDER BY r.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_pembayaran);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$riwayat = [];
$total_bayar = 0;

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $total_bayar += $row['jumlah_bayar'];
        
        $riwayat[] = [
            'tanggal_bayar' => date('d-m-Y', strtotime($row['tanggal_bayar'])),
            'jumlah_bayar' => $row['jumlah_bayar'],
            'jumlah_bayar_format' => 'Rp ' . number_format($row['jumlah_bayar'], 0, ',', '.'),
            'bukti_bayar' => $row['bukti_bayar'],
            'catatan' => $row['catatan'] ?: 'Pembayaran',
            'created_at' => date('d-m-Y H:i', strtotime($row['created_at']))
        ];
    }
}

// Get status pembayaran dari tabel utama
$query_status = "SELECT status_pembayaran, jumlah_pembayaran FROM pembayaran WHERE id_pembayaran = ?";
$stmt_status = mysqli_prepare($conn, $query_status);
mysqli_stmt_bind_param($stmt_status, "i", $id_pembayaran);
mysqli_stmt_execute($stmt_status);
$result_status = mysqli_stmt_get_result($stmt_status);
$status_row = mysqli_fetch_assoc($result_status);

$response = [
    'success' => true,
    'riwayat' => $riwayat,
    'total_bayar' => $status_row['jumlah_pembayaran'] ?? $total_bayar,
    'total_bayar_format' => 'Rp ' . number_format($status_row['jumlah_pembayaran'] ?? $total_bayar, 0, ',', '.'),
    'total_cicilan' => count($riwayat),
    'status' => $status_row['status_pembayaran'] ?? 'Belum Lunas'
];

echo json_encode($response);

mysqli_stmt_close($stmt);
mysqli_stmt_close($stmt_status);
mysqli_close($conn);
?>