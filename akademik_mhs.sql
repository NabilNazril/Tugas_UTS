CREATE DATABASE IF NOT EXISTS `akademik_mhs`;
USE `akademik_mhs`;

-- phpMyAdmin SQL Dump
-- Database: `akademik_mhs`
-- Updated: Tambah role mahasiswa pada tabel users

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table: mahasiswa
-- --------------------------------------------------------
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

-- --------------------------------------------------------
-- Table: users (role ditambah 'mahasiswa')
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hakakses` enum('admin','dosen','mahasiswa') NOT NULL DEFAULT 'dosen',
  `nim` varchar(20) DEFAULT NULL COMMENT 'Diisi jika role mahasiswa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Sample Data
-- --------------------------------------------------------

-- Password semua: password123 (di-hash dengan password_hash PHP)
INSERT INTO `users` (`username`, `nama`, `password`, `hakakses`, `nim`) VALUES
('admin', 'Administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL),
('dosen01', 'Dr. Budi Santoso', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dosen', NULL),
('22001001', 'Ahmad Fauzi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '22001001'),
('22001002', 'Siti Rahayu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mahasiswa', '22001002');

-- Sample data mahasiswa
INSERT INTO `mahasiswa` (`nim`, `nama`, `program_studi`, `semester`, `mata_kuliah`, `sks`, `nilai_tugas`, `nilai_uts`, `nilai_uas`, `nilai_akhir`, `huruf`, `bobot`) VALUES
('22001001', 'Ahmad Fauzi', 'Teknik Informatika', 4, 'Pemrograman Web', 3, 85.00, 80.00, 88.00, 84.70, 'A', 4.00),
('22001001', 'Ahmad Fauzi', 'Teknik Informatika', 4, 'Basis Data', 3, 78.00, 75.00, 80.00, 77.90, 'B+', 3.50),
('22001001', 'Ahmad Fauzi', 'Teknik Informatika', 4, 'Jaringan Komputer', 2, 70.00, 72.00, 68.00, 70.20, 'B', 3.00),
('22001002', 'Siti Rahayu', 'Sistem Informasi', 4, 'Pemrograman Web', 3, 90.00, 88.00, 92.00, 90.20, 'A', 4.00),
('22001002', 'Siti Rahayu', 'Sistem Informasi', 4, 'Basis Data', 3, 82.00, 85.00, 83.00, 83.30, 'A-', 3.75);

-- --------------------------------------------------------
-- Indexes & AUTO_INCREMENT
-- --------------------------------------------------------
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim_matkul` (`nim`, `mata_kuliah`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `mahasiswa` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

COMMIT;
