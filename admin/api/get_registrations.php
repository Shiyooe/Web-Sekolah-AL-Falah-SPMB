<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

$query = "SELECT 
    p.id_pendaftaran,
    p.nama, 
    p.nisn, 
    p.jenis_kelamin, 
    TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) as umur,
    p.asal_sekolah,
    p.tgl_daftar,
    COALESCE(pb.status_pembayaran, 'Belum Bayar') as status_pembayaran
FROM pendaftaran p
LEFT JOIN pembayaran pb ON p.id_pendaftaran = pb.id_pendaftaran
ORDER BY p.tgl_daftar DESC";

$result = mysqli_query($conn, $query);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);