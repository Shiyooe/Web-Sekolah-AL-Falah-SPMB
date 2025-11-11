<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$stats = [];

// Total Pendaftar
$query = "SELECT COUNT(*) as total FROM pendaftaran";
$result = mysqli_query($conn, $query);
$stats['total_pendaftar'] = mysqli_fetch_assoc($result)['total'];

// Sudah Bayar
$query = "SELECT COUNT(*) as total FROM pembayaran";
$result = mysqli_query($conn, $query);
$stats['sudah_bayar'] = mysqli_fetch_assoc($result)['total'];

// Lunas
$query = "SELECT COUNT(*) as total FROM pembayaran WHERE status_pembayaran = 'Lunas'";
$result = mysqli_query($conn, $query);
$stats['lunas'] = mysqli_fetch_assoc($result)['total'];

// Belum Bayar
$query = "SELECT COUNT(*) as total FROM pendaftaran p LEFT JOIN pembayaran pb ON p.id_pendaftaran = pb.id_pendaftaran WHERE pb.id_pembayaran IS NULL";
$result = mysqli_query($conn, $query);
$stats['belum_bayar'] = mysqli_fetch_assoc($result)['total'];

echo json_encode($stats);