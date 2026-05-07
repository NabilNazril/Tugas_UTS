-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2026 at 10:05 AM
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
-- Database: `akademik_sistem`
--

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `program_studi` varchar(60) NOT NULL,
  `semester` tinyint(4) NOT NULL DEFAULT 1,
  `mata_kuliah` varchar(80) NOT NULL,
  `sks` tinyint(4) NOT NULL DEFAULT 3,
  `nilai_tugas` decimal(5,2) DEFAULT 0.00,
  `nilai_uts` decimal(5,2) DEFAULT 0.00,
  `nilai_uas` decimal(5,2) DEFAULT 0.00,
  `nilai_akhir` decimal(5,2) DEFAULT 0.00,
  `huruf` char(2) DEFAULT '-',
  `bobot` decimal(3,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nim`, `nama`, `program_studi`, `semester`, `mata_kuliah`, `sks`, `nilai_tugas`, `nilai_uts`, `nilai_uas`, `nilai_akhir`, `huruf`, `bobot`, `created_at`, `updated_at`) VALUES
(1, '122419855', 'Ahmad Fauzi', 'Teknik Informatika', 4, 'Pemrograman Web', 3, 85.00, 80.00, 88.00, 84.70, 'A-', 3.75, '2026-05-04 12:00:12', '2026-05-07 07:30:01'),
(2, '12241955', 'Ahmad Fauzi', 'Teknik Informatika', 4, 'Basis Data', 3, 78.00, 75.00, 80.00, 77.90, 'B+', 3.50, '2026-05-04 12:00:12', '2026-05-07 07:29:21'),
(3, '12241955', 'Ahmad Fauzi', 'Teknik Informatika', 4, 'Jaringan Komputer', 2, 70.00, 72.00, 68.00, 69.80, 'B-', 2.75, '2026-05-04 12:00:12', '2026-05-07 07:29:40'),
(4, '11241645', 'Siti Rahayu', 'Sistem Informasi', 4, 'Pemrograman Web', 3, 90.00, 88.00, 92.00, 90.20, 'A', 4.00, '2026-05-04 12:00:12', '2026-05-07 07:30:44'),
(6, '12241985', 'Nabil Nazril Fikar', 'Teknil Informatika', 4, 'Pemrograman Web', 4, 90.00, 90.00, 90.00, 90.00, 'A', 4.00, '2026-05-07 07:28:44', '2026-05-07 07:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hakakses` enum('admin','dosen','mahasiswa') NOT NULL DEFAULT 'dosen',
  `nim` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama`, `password`, `hakakses`, `nim`, `created_at`) VALUES
(1, 'admin', 'Administrator', '$2y$10$T5fVXdQUMuZBnrzkWD87N.b6ub6tGnmP3k8YP2wolrkRUOCt1uBQG', 'admin', NULL, '2026-05-04 12:00:12'),
(7, 'dosen01', 'dosenn', '$2y$10$urMSuq84rOeu7bqmQX9I4eaVHnXnG7B.zOq3FmGxtraXk4Ng6IzAW', 'dosen', NULL, '2026-05-04 12:31:42'),
(8, '12241985', 'Nabil Nazril Fikar', '$2y$10$mgzRRYfF.MNoYuMCn4d0Au5Bm7UVV34aye8v3aWZC5RUdlENvFDme', 'mahasiswa', '12241985', '2026-05-07 07:36:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim_matkul` (`nim`,`mata_kuliah`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
