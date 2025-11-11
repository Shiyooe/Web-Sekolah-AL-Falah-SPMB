<?php
session_start();

// Cek login
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

$host = "localhost";
$user = "root"; 
$password = ""; 
$database = "spmb_alfalah"; 
$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Query data
$sql = "SELECT
            p.id_pendaftaran, p.nama, p.tgl_daftar, p.tempat_lahir, p.tanggal_lahir, p.anak_ke, p.jenis_kelamin, p.alamat, p.telepon AS No_Telepon, p.asal_sekolah, p.nisn, p.hobby, p.citacita, p.ukuran_baju, p.no_kk,
            p.nama_ayah, p.pekerjaan_ayah, p.tempat_lahir_ayah, p.tanggal_lahir_ayah, p.ktp_ayah, p.telepon_ayah,
            p.nama_ibu, p.pekerjaan_ibu, p.tempat_lahir_ibu, p.tanggal_lahir_ibu, p.ktp_ibu, p.telepon_ibu,
            j.jurusan1 AS Jurusan_1, j.jurusan2 AS Jurusan_2,
            w.nama_wali, w.pekerjaan_wali AS Pekerjaan_Wali, w.tempat_lahir_wali AS Tempat_Lahir_Wali, w.tanggal_lahir_wali AS Tanggal_Lahir_Wali, w.ktp_wali AS KTP_Wali, w.no_tlp_wali AS Telepon_Wali,
            b.Tanggal_pembayaran, b.jumlah_pembayaran AS Jumlah_Pembayaran, b.status_pembayaran AS Status_pembayaran
        FROM pendaftaran p
        LEFT JOIN jurusan j ON p.id_pendaftaran = j.id_pendaftaran
        LEFT JOIN wali w ON p.id_pendaftaran = w.id_pendaftaran
        LEFT JOIN pembayaran b ON p.id_pendaftaran = b.id_pendaftaran
        ORDER BY p.id_pendaftaran DESC";

$result = $koneksi->query($sql);

// Get last update time
$query_last_update = "SELECT MAX(tgl_daftar) as last_update FROM pendaftaran";
$result_last_update = $koneksi->query($query_last_update);
$last_update_row = $result_last_update->fetch_assoc();
$last_update = $last_update_row['last_update'];
$last_update_formatted = $last_update ? date('d/m/Y H:i:s', strtotime($last_update)) : 'No data';

// Build HTML
$html = '';
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jumlah_pembayaran_formatted = $row["Jumlah_Pembayaran"] ? number_format($row["Jumlah_Pembayaran"], 0, ',', '.') : '-';

        $html .= "<tr>";
        $html .= "<td>" . htmlspecialchars($row["id_pendaftaran"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["nama"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tgl_daftar"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Jurusan_1"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Jurusan_2"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tempat_lahir"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tanggal_lahir"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["anak_ke"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["jenis_kelamin"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["alamat"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["No_Telepon"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["asal_sekolah"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["nisn"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["hobby"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["citacita"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["ukuran_baju"]) . "</td>";
        $html .= "<td>|</td>";
        $html .= "<td>" . htmlspecialchars($row["no_kk"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["nama_ayah"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["pekerjaan_ayah"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tempat_lahir_ayah"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tanggal_lahir_ayah"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["ktp_ayah"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["telepon_ayah"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["nama_ibu"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["pekerjaan_ibu"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tempat_lahir_ibu"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["tanggal_lahir_ibu"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["ktp_ibu"]) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["telepon_ibu"] ?? '-') . "</td>";
        $html .= "<td>|</td>";
        $html .= "<td>" . htmlspecialchars($row["nama_wali"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Pekerjaan_Wali"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Tempat_Lahir_Wali"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Tanggal_Lahir_Wali"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["KTP_Wali"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Telepon_Wali"] ?? '-') . "</td>";
        $html .= "<td>|</td>";
        $html .= "<td>" . htmlspecialchars($row["Tanggal_pembayaran"] ?? '-') . "</td>";
        $html .= "<td>" . htmlspecialchars($jumlah_pembayaran_formatted) . "</td>";
        $html .= "<td>" . htmlspecialchars($row["Status_pembayaran"] ?? '-') . "</td>";
        $html .= "</tr>";
    }
} else {
    $html = "<tr><td colspan='40' class='text-center'>Tidak ada data</td></tr>";
}

$koneksi->close();

echo json_encode([
    'success' => true,
    'html' => $html,
    'last_update' => $last_update_formatted,
    'total_rows' => $result->num_rows
]);
?>