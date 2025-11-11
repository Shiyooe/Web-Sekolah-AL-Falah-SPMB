-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 09:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spmb_alfalah`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `first_name`, `last_name`, `email`, `password`) VALUES
(1, 'admin', 'Syia', 'syia@gmail.com', '123456'),
(5, 'Admin', 'Shiyooe', 'akrommuhammadyusuf@gmail.com', 'Femboy12');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id_jurusan` int(11) NOT NULL,
  `id_pendaftaran` int(11) NOT NULL,
  `jurusan1` varchar(50) NOT NULL,
  `jurusan2` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id_jurusan`, `id_pendaftaran`, `jurusan1`, `jurusan2`) VALUES
(16, 1, 'RPL', 'Listrik'),
(17, 2, 'Mesin', 'RPL'),
(20, 5, 'RPL', 'Listrik'),
(21, 6, 'Mesin', 'RPL'),
(22, 7, 'Otomotif', 'Mesin');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pendaftaran` int(11) DEFAULT NULL,
  `Tanggal_pembayaran` date NOT NULL,
  `jumlah_pembayaran` bigint(20) NOT NULL,
  `status_pembayaran` varchar(255) NOT NULL,
  `bukti_pembayaran` text DEFAULT NULL COMMENT 'Path foto bukti pembayaran (dipisah koma jika lebih dari 1)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pendaftaran`, `Tanggal_pembayaran`, `jumlah_pembayaran`, `status_pembayaran`, `bukti_pembayaran`) VALUES
(9, 1, '2025-11-08', 3350000, 'Lunas', 'uploads/bukti_pembayaran/bukti_1_1762629477.jpg'),
(10, 2, '2025-11-09', 3350000, 'Lunas', 'uploads/bukti_pembayaran/bukti_2_1762661058.png'),
(11, 6, '2025-11-09', 3500000, 'Lunas', 'uploads/bukti_pembayaran/bukti_6_1762662091.png'),
(12, 7, '2025-11-09', 2000000, 'Belum Lunas', 'uploads/bukti_pembayaran/bukti_7_1762670646.png');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `tgl_daftar` date NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tempat_lahir` text NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `anak_ke` int(15) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `asal_sekolah` varchar(255) NOT NULL,
  `nisn` bigint(30) NOT NULL,
  `hobby` varchar(255) NOT NULL,
  `citacita` varchar(255) NOT NULL,
  `ukuran_baju` enum('S','M','L','XL','XXL','XXXL') NOT NULL,
  `no_kk` bigint(30) NOT NULL,
  `nama_ayah` varchar(255) NOT NULL,
  `pekerjaan_ayah` varchar(255) DEFAULT NULL,
  `tempat_lahir_ayah` varchar(255) NOT NULL,
  `tanggal_lahir_ayah` date NOT NULL,
  `ktp_ayah` bigint(30) NOT NULL,
  `telepon_ayah` varchar(30) DEFAULT NULL,
  `nama_ibu` varchar(255) DEFAULT NULL,
  `pekerjaan_ibu` varchar(255) DEFAULT NULL,
  `tempat_lahir_ibu` varchar(255) NOT NULL,
  `tanggal_lahir_ibu` date NOT NULL,
  `ktp_ibu` bigint(30) NOT NULL,
  `telepon_ibu` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `tgl_daftar`, `nama`, `tempat_lahir`, `tanggal_lahir`, `anak_ke`, `jenis_kelamin`, `alamat`, `telepon`, `asal_sekolah`, `nisn`, `hobby`, `citacita`, `ukuran_baju`, `no_kk`, `nama_ayah`, `pekerjaan_ayah`, `tempat_lahir_ayah`, `tanggal_lahir_ayah`, `ktp_ayah`, `telepon_ayah`, `nama_ibu`, `pekerjaan_ibu`, `tempat_lahir_ibu`, `tanggal_lahir_ibu`, `ktp_ibu`, `telepon_ibu`) VALUES
(1, '2025-09-29', 'Muhammad Yusuf Akram', 'Bandung Cisitu lama ', '2008-08-12', 1, 'Laki-laki', 'bandung, Coblong, dago pojok jln.bunisari', '+62 821-2054-7015', 'SMPN 35', 327302120808002, 'Sepedaan, Masak ,Lari, Gaming, Alam', 'Aerospace Enginner muda', 'M', 9223372036854775807, 'Abi', 'Dosen', 'Malang, Dampit', '1981-12-20', 7827346234827, '+62 895-3105-0229', 'Umi', 'Ibu Rumah Tangga', 'Surabaya Siduarjo', '1981-01-29', 327348726478234, '+62 852-2235-7550'),
(2, '2025-10-04', 'Iuno', 'Bandung Dago', '2007-02-12', 1, 'Perempuan', 'bandung, Coblong, dago pojok jln.bunisari', '0812-3456-7890', 'SMPN 35', 324543544353543, 'Makan, Fotografi', 'Menjadi yg terkuat ', 'L', 0, 'N/A', NULL, 'N/A', '1000-01-01', 0, '', NULL, NULL, 'N/A', '1000-01-01', 0, ''),
(5, '2025-10-07', 'Rival ', 'Gerlong ', '2007-10-10', 2, 'Laki-laki', 'Jln Gerlong tengah ', '08675456456', 'smp', 76857564645345, 'sepedaan', 'Buka toko sepeda', 'M', 685765734532445564, 'ayah', 'buruh ', 'bandung ', '1980-02-12', 4324326767576, '0832565235', 'ibu', 'ibu rumah tangga', 'bandung', '1981-05-02', 9084786273264732, '+62 657-454-545'),
(6, '2025-11-09', 'Elia', 'Bandung Dago', '2009-12-03', 3, 'Perempuan', 'bandung, Coblong, dago pojok ', '083324422424', 'SMPN 22', 324543544353333, 'Badminton', 'atlet', 'M', 0, 'N/A', NULL, 'N/A', '1000-01-01', 0, '', NULL, NULL, 'N/A', '1000-01-01', 0, ''),
(7, '2025-11-09', 'Kyota', 'Jepang. Shibuya', '2009-10-12', 1, 'Laki-laki', 'Bandung. Dago', '+81 90-1234-5678', 'Montessori School of Tokyo', 7876857565645243, 'Desain ', 'Ilustrator', 'L', 2398178310023878, 'Hajime oka', 'Pembisnis', 'Jepang.Kyoto', '1990-12-21', 9239788324784323, '+81 90-9865-4832', 'Yuki ', 'ART', 'Jepang.Tokyo', '1990-01-03', 4387947982894730, '+81 90-3422-4834');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `gmail` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `gmail`, `password`) VALUES
(2, 'Akrom', 'akrommuhammadyusuf@gmail.com', '12345678'),
(3, 'Udin Ansur', 'Udin@gmail.com', 'sukablyat'),
(4, 'Rival', 'Rival@gmail.com', '1234566'),
(5, 'Yanto', 'Yanto@gmail.com', '654321');

-- --------------------------------------------------------

--
-- Table structure for table `wali`
--

CREATE TABLE `wali` (
  `id_wali` int(11) NOT NULL,
  `id_pendaftaran` int(11) NOT NULL,
  `nama_wali` varchar(255) NOT NULL,
  `tempat_lahir_wali` text NOT NULL,
  `tanggal_lahir_wali` date NOT NULL,
  `ktp_wali` bigint(30) DEFAULT NULL,
  `no_tlp_wali` varchar(30) NOT NULL,
  `pekerjaan_wali` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wali`
--

INSERT INTO `wali` (`id_wali`, `id_pendaftaran`, `nama_wali`, `tempat_lahir_wali`, `tanggal_lahir_wali`, `ktp_wali`, `no_tlp_wali`, `pekerjaan_wali`) VALUES
(6, 2, 'siti ', 'bandung ', '1992-12-31', 3892984634, '0812902834', 'Nguli'),
(9, 6, 'sinta', 'DKI jakarta', '1974-03-22', 34234456765657, '81232456678', 'CEO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id_jurusan`),
  ADD KEY `fk_pendaftaran` (`id_pendaftaran`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_id_pendaftaran` (`id_pendaftaran`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wali`
--
ALTER TABLE `wali`
  ADD PRIMARY KEY (`id_wali`),
  ADD KEY `fk_wali_pendaftaran` (`id_pendaftaran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id_jurusan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wali`
--
ALTER TABLE `wali`
  MODIFY `id_wali` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD CONSTRAINT `fk_pendaftaran` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_id_pendaftaran` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wali`
--
ALTER TABLE `wali`
  ADD CONSTRAINT `fk_wali_pendaftaran` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
