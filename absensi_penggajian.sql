-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 19, 2026 at 03:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_penggajian`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) DEFAULT NULL,
  `id_jadwal` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('masuk','keluar','tidak_hadir','izin','sakit') DEFAULT 'tidak_hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `id_karyawan`, `id_jadwal`, `tanggal`, `jam_masuk`, `jam_keluar`, `status`) VALUES
(1, 1, 101, '2026-01-01', '06:50:00', '15:10:00', 'keluar'),
(2, 1, 102, '2026-01-02', '07:00:00', '18:00:00', 'keluar'),
(3, 1, 103, '2026-01-03', '06:55:00', '15:00:00', 'keluar'),
(4, 1, 105, '2026-01-05', '14:55:00', '23:00:00', 'keluar'),
(5, 1, 106, '2026-01-06', '15:00:00', '01:00:00', 'keluar'),
(6, 1, 107, '2026-01-07', '15:10:00', '23:05:00', 'keluar'),
(7, 1, 109, '2026-01-09', '06:45:00', '15:00:00', 'keluar'),
(8, 1, 113, '2026-01-13', '14:50:00', '23:00:00', 'keluar'),
(9, 1, 114, '2026-01-14', '15:00:00', '23:00:00', 'keluar'),
(10, 1, 115, '2026-01-15', '15:00:00', '00:30:00', 'keluar'),
(11, 1, 117, '2026-01-17', '06:50:00', '15:00:00', 'keluar'),
(12, 1, 118, '2026-01-18', '07:00:00', '15:00:00', 'keluar'),
(13, 1, 119, '2026-01-19', '07:05:00', '15:00:00', 'keluar'),
(14, 1, 121, '2026-01-21', '14:45:00', '23:00:00', 'keluar'),
(15, 1, 122, '2026-01-22', '14:55:00', '23:15:00', 'keluar'),
(16, 1, 123, '2026-01-23', '15:00:00', '23:00:00', 'keluar'),
(17, 1, 125, '2026-01-25', '06:55:00', '17:00:00', 'keluar'),
(18, 1, 126, '2026-01-26', '07:00:00', '15:00:00', 'keluar'),
(19, 1, 129, '2026-01-29', '15:00:00', '23:00:00', 'keluar'),
(20, 1, 130, '2026-01-30', '14:50:00', '23:00:00', 'keluar'),
(21, 1, 131, '2026-01-31', '15:00:00', '00:00:00', 'keluar');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$SokEaMOqlhU1J2VqZ2U5guJRlFvceZQ2I2z02CaK2TPea658B7AYW');

-- --------------------------------------------------------

--
-- Table structure for table `izin_keluar`
--

CREATE TABLE `izin_keluar` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_pergi` time NOT NULL,
  `jam_kembali` time DEFAULT NULL,
  `keperluan` text DEFAULT NULL,
  `status_izin` enum('proses','kembali') DEFAULT 'proses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `izin_keluar`
--

INSERT INTO `izin_keluar` (`id`, `id_karyawan`, `tanggal`, `jam_pergi`, `jam_kembali`, `keperluan`, `status_izin`) VALUES
(1, 1, '2026-03-06', '15:39:00', NULL, 'urursan', 'proses'),
(2, 2, '2026-01-08', '15:40:00', '15:40:33', 'izin', 'kembali');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_kerja`
--

CREATE TABLE `jadwal_kerja` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) DEFAULT NULL,
  `id_shift` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_kerja`
--

INSERT INTO `jadwal_kerja` (`id`, `id_karyawan`, `id_shift`, `tanggal`) VALUES
(101, 1, 1, '2026-01-01'),
(102, 1, 1, '2026-01-02'),
(103, 1, 1, '2026-01-03'),
(104, 1, 3, '2026-01-04'),
(105, 1, 2, '2026-01-05'),
(106, 1, 2, '2026-01-06'),
(107, 1, 2, '2026-01-07'),
(108, 1, 3, '2026-01-08'),
(109, 1, 1, '2026-01-09'),
(110, 1, 1, '2026-01-10'),
(111, 1, 1, '2026-01-11'),
(112, 1, 3, '2026-01-12'),
(113, 1, 2, '2026-01-13'),
(114, 1, 2, '2026-01-14'),
(115, 1, 2, '2026-01-15'),
(116, 1, 3, '2026-01-16'),
(117, 1, 1, '2026-01-17'),
(118, 1, 1, '2026-01-18'),
(119, 1, 1, '2026-01-19'),
(120, 1, 3, '2026-01-20'),
(121, 1, 2, '2026-01-21'),
(122, 1, 2, '2026-01-22'),
(123, 1, 2, '2026-01-23'),
(124, 1, 3, '2026-01-24'),
(125, 1, 1, '2026-01-25'),
(126, 1, 1, '2026-01-26'),
(127, 1, 1, '2026-01-27'),
(128, 1, 3, '2026-01-28'),
(129, 1, 2, '2026-01-29'),
(130, 1, 2, '2026-01-30'),
(131, 1, 2, '2026-01-31');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `bagian` varchar(50) DEFAULT NULL,
  `gaji_pokok` decimal(15,2) DEFAULT 0.00,
  `tunjangan_tetap` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama`, `username`, `password`, `jabatan`, `bagian`, `gaji_pokok`, `tunjangan_tetap`) VALUES
(1, '3123282746', 'Umul Amalah', 'umul', '$2y$10$Mvt2vDOlS2v9vE/vqQ2i9.8IW9skmow05rGDXHuE/D5pu4.pPICcC', 'Staff', 'QC', 2000000.00, 0.00),
(2, '238763728746', 'Ardiansyah', 'ardian', '$2y$10$O0SooQZXnjGURZKvUliLROsOPQjV8yP5bissl0sSuWqQq1VpOc37.', 'Staff', 'QC', 1200000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `penggajian`
--

CREATE TABLE `penggajian` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) DEFAULT NULL,
  `bulan` tinyint(4) NOT NULL,
  `tahun` int(4) NOT NULL,
  `total_gaji_pokok` decimal(15,2) DEFAULT NULL,
  `insentif` decimal(15,2) DEFAULT 0.00,
  `prestasi` decimal(15,2) DEFAULT 0.00,
  `lembur` decimal(15,2) DEFAULT 0.00,
  `subsidi_transport` decimal(15,2) DEFAULT 0.00,
  `subsidi_makan` decimal(15,2) DEFAULT 0.00,
  `potongan_mangkir_sakit` decimal(15,2) DEFAULT 0.00,
  `potongan_bpjs` decimal(15,2) DEFAULT 0.00,
  `potongan_jht` decimal(15,2) DEFAULT 0.00,
  `total_terima` decimal(15,2) DEFAULT NULL,
  `tanggal_generate` timestamp NOT NULL DEFAULT current_timestamp(),
  `jml_transport` int(11) DEFAULT 0,
  `jml_makan` int(11) DEFAULT 0,
  `jml_lembur_1` int(11) DEFAULT 0,
  `jml_lembur_2` int(11) DEFAULT 0,
  `jml_mangkir` int(11) DEFAULT 0,
  `jml_izin_keluar` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penggajian`
--

INSERT INTO `penggajian` (`id`, `id_karyawan`, `bulan`, `tahun`, `total_gaji_pokok`, `insentif`, `prestasi`, `lembur`, `subsidi_transport`, `subsidi_makan`, `potongan_mangkir_sakit`, `potongan_bpjs`, `potongan_jht`, `total_terima`, `tanggal_generate`, `jml_transport`, `jml_makan`, `jml_lembur_1`, `jml_lembur_2`, `jml_mangkir`, `jml_izin_keluar`) VALUES
(1, 1, 1, 2026, 2000000.00, 5334.00, 5334.00, 194310.00, 42000.00, 63000.00, 166000.00, 0.00, 0.00, 2122642.00, '2026-01-09 12:57:25', 21, 21, 9, 0, 2, 0),
(2, 2, 1, 2026, 1200000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1200000.00, '2026-01-09 12:39:41', 0, 0, 0, 0, 0, 0),
(10, 1, 3, 2026, 2000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2000000.00, '2026-01-19 01:55:29', 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `nama_shift` varchar(20) NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `nama_shift`, `jam_masuk`, `jam_keluar`) VALUES
(1, 'Pagi', '07:00:00', '15:00:00'),
(2, 'Siang', '15:00:00', '23:00:00'),
(3, 'OFF', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `izin_keluar`
--
ALTER TABLE `izin_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_karyawan_izin` (`id_karyawan`);

--
-- Indexes for table `jadwal_kerja`
--
ALTER TABLE `jadwal_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`),
  ADD KEY `id_shift` (`id_shift`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `izin_keluar`
--
ALTER TABLE `izin_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jadwal_kerja`
--
ALTER TABLE `jadwal_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penggajian`
--
ALTER TABLE `penggajian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_kerja` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `izin_keluar`
--
ALTER TABLE `izin_keluar`
  ADD CONSTRAINT `fk_karyawan_izin` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jadwal_kerja`
--
ALTER TABLE `jadwal_kerja`
  ADD CONSTRAINT `jadwal_kerja_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_kerja_ibfk_2` FOREIGN KEY (`id_shift`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD CONSTRAINT `penggajian_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
