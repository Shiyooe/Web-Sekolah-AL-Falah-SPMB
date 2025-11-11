<?php
// File untuk debug - simpan di folder Tugas PPDB
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Riwayat Pembayaran</h2>";

// Test koneksi
require_once '../config/koneksi3.php';

if ($conn) {
    echo "<p style='color: green;'>✓ Koneksi database berhasil</p>";
} else {
    echo "<p style='color: red;'>✗ Koneksi database gagal</p>";
    exit;
}

// Cek tabel riwayat_pembayaran
$check_table = "SHOW TABLES LIKE 'riwayat_pembayaran'";
$result = mysqli_query($conn, $check_table);

if (mysqli_num_rows($result) > 0) {
    echo "<p style='color: green;'>✓ Tabel riwayat_pembayaran ada</p>";
} else {
    echo "<p style='color: red;'>✗ Tabel riwayat_pembayaran tidak ditemukan</p>";
    echo "<p>Silakan jalankan SQL update_database.sql terlebih dahulu</p>";
    exit;
}

// Cek data riwayat
$query_riwayat = "SELECT COUNT(*) as total FROM riwayat_pembayaran";
$result = mysqli_query($conn, $query_riwayat);
$row = mysqli_fetch_assoc($result);

echo "<p>Total data di riwayat_pembayaran: <strong>{$row['total']}</strong></p>";

// Cek data pembayaran
$query_pembayaran = "SELECT COUNT(*) as total FROM pembayaran";
$result = mysqli_query($conn, $query_pembayaran);
$row = mysqli_fetch_assoc($result);

echo "<p>Total data di pembayaran: <strong>{$row['total']}</strong></p>";

// Tampilkan sample data
echo "<h3>Sample Data Riwayat:</h3>";
$query = "SELECT 
    r.id_riwayat,
    r.id_pembayaran,
    p.nama,
    r.tanggal_bayar,
    r.jumlah_bayar,
    r.bukti_bayar,
    r.catatan
FROM riwayat_pembayaran r
INNER JOIN pendaftaran p ON r.id_pendaftaran = p.id_pendaftaran
ORDER BY r.id_riwayat DESC
LIMIT 5";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr>
            <th>ID</th>
            <th>ID Pembayaran</th>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Bukti</th>
            <th>Catatan</th>
          </tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $file_exists = file_exists('../' . $row['bukti_bayar']) ? '✓' : '✗';
        echo "<tr>
                <td>{$row['id_riwayat']}</td>
                <td>{$row['id_pembayaran']}</td>
                <td>{$row['nama']}</td>
                <td>{$row['tanggal_bayar']}</td>
                <td>Rp " . number_format($row['jumlah_bayar'], 0, ',', '.') . "</td>
                <td>{$file_exists} {$row['bukti_bayar']}</td>
                <td>{$row['catatan']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>Tidak ada data riwayat</p>";
}

// Test get_riwayat.php
echo "<h3>Test API get_riwayat.php:</h3>";
$test_id = 9; // Ganti dengan ID pembayaran yang ada
echo "<p><a href='get_riwayat.php?id=$test_id' target='_blank'>Test get_riwayat.php?id=$test_id</a></p>";

mysqli_close($conn);
?>