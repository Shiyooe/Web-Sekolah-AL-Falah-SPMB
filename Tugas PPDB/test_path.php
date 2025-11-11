<?php
// Simpan file ini di: Tugas PPDB/test_path.php
echo "<h2>Test Path File</h2>";

$test_paths = [
    'uploads/bukti_pembayaran/bukti_8_1762771537.png',
    '../uploads/bukti_pembayaran/bukti_8_1762771537.png',
    '../../uploads/bukti_pembayaran/bukti_8_1762771537.png',
];

echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

foreach ($test_paths as $path) {
    $full_path = __DIR__ . '/' . $path;
    $exists = file_exists($full_path);
    $color = $exists ? 'green' : 'red';
    $icon = $exists ? '✓' : '✗';
    
    echo "<p style='color: $color;'>$icon <strong>$path</strong></p>";
    
    if ($exists) {
        echo "<img src='$path' style='max-width: 200px; border: 2px solid green;'><br><br>";
    }
}

// Scan folder uploads
echo "<h3>Isi Folder uploads/bukti_pembayaran:</h3>";
$upload_dir = 'uploads/bukti_pembayaran/';
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>Folder tidak ditemukan: $upload_dir</p>";
    
    // Coba path lain
    $upload_dir2 = '../uploads/bukti_pembayaran/';
    if (is_dir($upload_dir2)) {
        echo "<p style='color: green;'>✓ Folder ditemukan di: $upload_dir2</p>";
        $files = scandir($upload_dir2);
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    }
}
?>