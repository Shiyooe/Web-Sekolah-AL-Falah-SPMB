<?php
require_once '../koneksi.php';
header('Content-Type: application/json');

// Area Chart Data
$query_area = "SELECT 
    DATE_FORMAT(tgl_daftar, '%Y-%m') as bulan,
    COUNT(*) as total
FROM pendaftaran
GROUP BY DATE_FORMAT(tgl_daftar, '%Y-%m')
ORDER BY bulan";

$result_area = mysqli_query($conn, $query_area);
$area_labels = [];
$area_data = [];

while ($row = mysqli_fetch_assoc($result_area)) {
    $area_labels[] = $row['bulan'];
    $area_data[] = $row['total'];
}

// Bar Chart Data
$query_bar = "SELECT 
    jurusan1 as jurusan,
    COUNT(*) as total
FROM jurusan
GROUP BY jurusan1
UNION ALL
SELECT 
    jurusan2 as jurusan,
    COUNT(*) as total
FROM jurusan
WHERE jurusan2 IS NOT NULL
GROUP BY jurusan2";

$result_bar = mysqli_query($conn, $query_bar);
$bar_labels = [];
$bar_data = [];
$jurusan_counts = [];

while ($row = mysqli_fetch_assoc($result_bar)) {
    if (isset($jurusan_counts[$row['jurusan']])) {
        $jurusan_counts[$row['jurusan']] += $row['total'];
    } else {
        $jurusan_counts[$row['jurusan']] = $row['total'];
    }
}

foreach ($jurusan_counts as $jurusan => $total) {
    $bar_labels[] = $jurusan;
    $bar_data[] = $total;
}

echo json_encode([
    'area_labels' => $area_labels,
    'area_data' => $area_data,
    'bar_labels' => $bar_labels,
    'bar_data' => $bar_data
]);