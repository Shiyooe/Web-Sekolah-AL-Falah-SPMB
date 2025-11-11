<?php

if (isset($_GET['fetch_table_data'])) {
    
    $servername = "127.0.0.1"; // atau "localhost"
    $username = "root";       // Nama pengguna database default
    $password = "";           // Kata sandi database default (biasanya kosong)
    $dbname = "spmb_alfalah";   // Nama database dari file .sql Anda

    // Buat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // --- Query SQL untuk mengambil data ---
    // Kita menggunakan JOIN untuk menggabungkan tabel pendaftaran dan jurusan
    $sql = "SELECT p.nama, j.jurusan1, p.jenis_kelamin, p.hobby, p.citacita 
            FROM pendaftaran AS p
            JOIN jurusan AS j ON p.id_pendaftaran = j.id_pendaftaran
            ORDER BY p.tgl_daftar DESC"; // Menampilkan pendaftar terbaru di atas

    $result = $conn->query($sql);

    $output = ""; // Variabel untuk menyimpan baris tabel HTML

    if ($result->num_rows > 0) {
        // Ambil data dari setiap baris
        while($row = $result->fetch_assoc()) {
            // Kita 'escape' data untuk keamanan sebelum menampilkannya di HTML
            $nama = htmlspecialchars($row["nama"]);
            $jurusan1 = htmlspecialchars($row["jurusan1"]);
            $jenis_kelamin = htmlspecialchars($row["jenis_kelamin"]);
            $hobby = htmlspecialchars($row["hobby"]);
            $citacita = htmlspecialchars($row["citacita"]);

            $output .= "<tr class='hover:bg-white/70 transition-all duration-300'>";
            $output .= "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm font-semibold text-gray-800'>{$nama}</div></td>";
            $output .= "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm text-gray-700'>{$jurusan1}</div></td>";
            $output .= "<td class='px-6 py-4 whitespace-nowrap'><span class='px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100/80 text-blue-800 backdrop-blur-sm'>{$jenis_kelamin}</span></td>";
            $output .= "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm text-gray-700'>{$hobby}</div></td>";
            $output .= "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm text-gray-700'>{$citacita}</div></td>";
            $output .= "</tr>";
        }
    } else {
        $output = "<tr><td colspan='5' class='px-6 py-8 text-center text-gray-600'><i class='fas fa-inbox mr-2'></i>Belum ada data pendaftar</td></tr>";
    }

    $conn->close();
    
    echo $output; // Kirimkan hanya data baris tabel
    exit; // Hentikan eksekusi script agar tidak mengirim sisa HTML
}

// Jika bukan permintaan fetch, lanjutkan untuk memuat seluruh halaman HTML di bawah ini
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Pendaftar - SMK Al Falah Bandung</title>
    <link rel="icon" type="x-icon" href="img/Alfalah.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Background dengan gambar dan overlay */
        body {
            background: 
                linear-gradient(
                    rgba(2, 30, 64, 0.85), 
                    rgba(5, 50, 100, 0.8)
                ),
                url('istockphoto-1489796304-170667a.jpg') center/cover fixed;
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Efek glassmorphism untuk konten utama */
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.3),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.2);
        }

        /* Header glass */
        .glass-header {
            background: rgba(2, 30, 64, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Tabel dengan efek glass */
        .glass-table {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* Animasi untuk loading data */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Animasi untuk refresh */
        @keyframes refreshSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .refreshing {
            animation: refreshSpin 1s linear infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="text-white">
    <!-- Header -->
    <header class="glass-header shadow-xl sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                    <img src="img/Alfalah.png" alt="Logo SMK Al Falah" class="h-8 w-8">
                </div>
                <div>
                    <h1 class="text-xl font-bold">SMK Al Falah Bandung</h1>
                    <p class="text-blue-200 text-sm">Tabel Data Pendaftar</p>
                </div>
            </div>
            
            <!-- Tombol Menu Toggle (Hamburger) untuk mobile -->
            <button id="menuToggle" class="md:hidden flex flex-col space-y-1 p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                <span class="w-6 h-0.5 bg-white rounded"></span>
                <span class="w-6 h-0.5 bg-white rounded"></span>
                <span class="w-6 h-0.5 bg-white rounded"></span>
            </button>
            
            <!-- Navigasi -->
            <nav id="mainNav" class="hidden md:flex space-x-1">
                <a href="index.html" class="px-4 py-2 rounded-lg hover:bg-white/10 transition-all duration-300">Beranda</a>
                
                <!-- Dropdown Profil -->
                <div class="relative group">
                    <a href="#" class="px-4 py-2 rounded-lg hover:bg-white/10 transition-all duration-300 flex items-center">
                        Profil <i class="fas fa-chevron-down ml-2 text-xs"></i>
                    </a>
                    <div class="absolute left-0 mt-2 w-56 glass-card py-2 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-white/10">
                        <a href="sejarah.html" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-history mr-2"></i>Sejarah</a>
                        <a href="visi.html" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-bullseye mr-2"></i>Visi Misi</a>
                        <a href="progam_kerja.html" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-tasks mr-2"></i>Program Kerja</a>
                        <a href="#" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-sitemap mr-2"></i>Struktur Organisasi</a>
                        <a href="#" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-video mr-2"></i>Profil Video</a>
                        <a href="#" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-building mr-2"></i>Fasilitas Sekolah</a>
                        <a href="#" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-users mr-2"></i>Direktori PTK</a>
                    </div>
                </div>
                
                <!-- Dropdown lainnya dengan gaya yang sama -->
                <div class="relative group">
                    <a href="#" class="px-4 py-2 rounded-lg hover:bg-white/10 transition-all duration-300 flex items-center">
                        Kurikulum <i class="fas fa-chevron-down ml-2 text-xs"></i>
                    </a>
                    <div class="absolute left-0 mt-2 w-48 glass-card py-2 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-white/10">
                        <a href="#" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-book mr-2"></i>Program Keahlian</a>
                        <a href="#" class="block px-4 py-2 text-white hover:bg-white/10"><i class="fas fa-calendar mr-2"></i>Jadwal Pelajaran</a>
                    </div>
                </div>
                
                <a href="tabel.php" class="px-4 py-2 rounded-lg bg-white/20 backdrop-blur-sm font-semibold border border-white/30 shadow-lg">
                    <i class="fas fa-table mr-2"></i>Tabel
                </a>
            </nav>
        </div>
        
        <!-- Menu Mobile -->
        <div id="mobileMenu" class="md:hidden glass-card mx-4 mt-2 p-4 hidden fade-in">
            <div class="flex flex-col space-y-2">
                <a href="index.html" class="px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-300 flex items-center">
                    <i class="fas fa-home mr-3"></i>Beranda
                </a>
                
                <!-- Dropdown Profil Mobile -->
                <div>
                    <button class="w-full px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-300 flex items-center justify-between" onclick="toggleMobileDropdown('profil-dropdown')">
                        <span><i class="fas fa-user mr-3"></i>Profil</span>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" id="profil-arrow"></i>
                    </button>
                    <div id="profil-dropdown" class="pl-8 mt-1 space-y-1 hidden">
                        <a href="sejarah.html" class="block px-4 py-2 rounded-lg hover:bg-white/10 text-sm"><i class="fas fa-history mr-2"></i>Sejarah</a>
                        <a href="visi.html" class="block px-4 py-2 rounded-lg hover:bg-white/10 text-sm"><i class="fas fa-bullseye mr-2"></i>Visi Misi</a>
                        <a href="progam_kerja.html" class="block px-4 py-2 rounded-lg hover:bg-white/10 text-sm"><i class="fas fa-tasks mr-2"></i>Program Kerja</a>
                    </div>
                </div>
                
                <a href="tabel.php" class="px-4 py-3 rounded-lg bg-white/20 font-semibold border border-white/30">
                    <i class="fas fa-table mr-3"></i>Tabel
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="container mx-auto px-4 py-8">
        <div class="glass-card p-6 fade-in">
            <!-- Header Tabel -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Data Pendaftar</h1>
                    <p class="text-blue-100 opacity-90">Daftar calon siswa yang telah mendaftar di SMK Al Falah Bandung</p>
                </div>
                
                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                    <div class="flex items-center text-blue-100 bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-sync-alt mr-2"></i>
                        <span class="text-sm">Auto-refresh setiap 2 menit</span>
                    </div>
                    <button id="refreshBtn" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-all duration-300 flex items-center backdrop-blur-sm border border-white/20 hover:border-white/30">
                        <i class="fas fa-redo-alt mr-2" id="refreshIcon"></i> Refresh
                    </button>
                </div>
            </div>
            
            <!-- Tabel -->
            <div class="overflow-x-auto rounded-xl shadow-2xl">
                <table class="min-w-full glass-table rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-white/20 backdrop-blur-md">
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-user-graduate mr-3 text-lg"></i>
                                    <span>Nama Peserta</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-book mr-3 text-lg"></i>
                                    <span>Jurusan</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-venus-mars mr-3 text-lg"></i>
                                    <span>Jenis Kelamin</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-heart mr-3 text-lg"></i>
                                    <span>Hobby</span>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                <div class="flex items-center text-white">
                                    <i class="fas fa-star mr-3 text-lg"></i>
                                    <span>Cita-cita</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="pendaftar-table-body" class="divide-y divide-white/10">
                        <?php
                        // --- Blok PHP untuk Pemuatan Awal ---
                        // Bagian ini mengisi tabel saat halaman pertama kali dibuka.

                        // --- Konfigurasi Database ---
                        $servername = "127.0.0.1";
                        $username = "root";
                        $password = "";
                        $dbname = "spmb_alfalah";

                        // Buat koneksi
                        $conn = new mysqli($servername, $username, $password, $dbname);

                        // Periksa koneksi
                        if ($conn->connect_error) {
                            die("Koneksi gagal: " . $conn->connect_error);
                        }

                        // --- Query SQL (sama seperti di atas) ---
                        $sql = "SELECT p.nama, j.jurusan1, p.jenis_kelamin, p.hobby, p.citacita 
                                FROM pendaftaran AS p
                                JOIN jurusan AS j ON p.id_pendaftaran = j.id_pendaftaran
                                ORDER BY p.tgl_daftar DESC";

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Ambil data dari setiap baris
                            while($row = $result->fetch_assoc()) {
                                // Escape data untuk keamanan
                                $nama = htmlspecialchars($row["nama"]);
                                $jurusan1 = htmlspecialchars($row["jurusan1"]);
                                $jenis_kelamin = htmlspecialchars($row["jenis_kelamin"]);
                                $hobby = htmlspecialchars($row["hobby"]);
                                $citacita = htmlspecialchars($row["citacita"]);

                                // Tampilkan baris tabel
                                echo "<tr class='hover:bg-white/20 transition-all duration-300 fade-in'>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm font-semibold text-white'>{$nama}</div></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm text-blue-100'>{$jurusan1}</div></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><span class='px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-white/20 text-white backdrop-blur-sm border border-white/20'>{$jenis_kelamin}</span></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm text-blue-100'>{$hobby}</div></td>";
                                echo "<td class='px-6 py-4 whitespace-nowrap'><div class='text-sm text-blue-100'>{$citacita}</div></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr class='fade-in'><td colspan='5' class='px-6 py-8 text-center text-blue-100'><i class='fas fa-inbox mr-2 text-2xl mb-2 block'></i>Belum ada data pendaftar</td></tr>";
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Footer Tabel -->
            <div class="mt-6 flex flex-col md:flex-row justify-between items-center text-sm text-blue-100">
                <div class="mb-3 md:mb-0 flex items-center bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-clock mr-2"></i>
                    <span>Terakhir diperbarui: <span id="updateTime" class="font-semibold"><?php echo date('H:i:s'); ?></span></span>
                </div>
                <div class="flex items-center bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-database mr-2"></i>
                    <span>Total: <span id="totalRecords" class="font-semibold"><?php echo $result->num_rows ?? 0; ?></span> pendaftar</span>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Menunggu seluruh dokumen HTML dimuat
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. Logika untuk Menu Toggle (Hamburger) ---
            const menuToggle = document.getElementById('menuToggle');
            const mobileMenu = document.getElementById('mobileMenu');

            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                // Animasi hamburger icon
                const spans = menuToggle.querySelectorAll('span');
                if (!mobileMenu.classList.contains('hidden')) {
                    spans[0].style.transform = 'rotate(45deg) translate(6px, 6px)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg) translate(6px, -6px)';
                } else {
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });

            // Fungsi untuk toggle dropdown di mobile
            window.toggleMobileDropdown = function(id) {
                const dropdown = document.getElementById(id);
                const arrow = document.getElementById(id.replace('dropdown', 'arrow'));
                dropdown.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            }

            // --- 2. Logika "Realtime" untuk Refresh Tabel ---
            
            // Fungsi untuk memperbarui waktu terakhir update
            function updateLastRefreshTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit',
                    second: '2-digit'
                });
                document.getElementById('updateTime').textContent = timeString;
            }

            // Fungsi untuk mengambil data tabel terbaru
            async function fetchTableData() {
                try {
                    // Tampilkan indikator loading
                    const refreshIcon = document.getElementById('refreshIcon');
                    refreshIcon.classList.add('refreshing');
                    
                    // Panggil file ini sendiri, tapi dengan parameter khusus
                    const response = await fetch('tabel.php?fetch_table_data=true');
                    
                    if (!response.ok) {
                        throw new Error('Gagal mengambil data: ' + response.statusText);
                    }

                    const newTableRows = await response.text();
                    
                    // Ganti isi <tbody> dengan data baru
                    const tableBody = document.getElementById('pendaftar-table-body');
                    if (tableBody) {
                        tableBody.innerHTML = newTableRows;
                        
                        // Tambahkan animasi fade-in ke baris baru
                        const newRows = tableBody.querySelectorAll('tr');
                        newRows.forEach(row => {
                            row.classList.add('fade-in');
                        });
                        
                        // Perbarui jumlah total record
                        const rows = tableBody.querySelectorAll('tr');
                        const totalRecords = rows.length - (rows.length > 0 && rows[0].querySelector('td[colspan]') ? 1 : 0);
                        document.getElementById('totalRecords').textContent = totalRecords;
                        
                        // Perbarui waktu terakhir update
                        updateLastRefreshTime();
                    }

                } catch (error) {
                    console.error('Error saat refresh tabel:', error);
                    // Tampilkan pesan error
                    const tableBody = document.getElementById('pendaftar-table-body');
                    tableBody.innerHTML = '<tr class="fade-in"><td colspan="5" class="px-6 py-8 text-center text-red-300"><i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat data: ' + error.message + '</td></tr>';
                } finally {
                    // Hentikan animasi loading
                    const refreshIcon = document.getElementById('refreshIcon');
                    refreshIcon.classList.remove('refreshing');
                }
            }

            // Atur interval untuk me-refresh data setiap 2 menit (120000 milidetik)
            let refreshInterval = setInterval(fetchTableData, 120000);
            
            // Tambahkan event listener untuk tombol refresh manual
            document.getElementById('refreshBtn').addEventListener('click', function() {
                fetchTableData();
            });

            // Perbarui waktu awal
            updateLastRefreshTime();
        });
    </script>
</body>

</html>