-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Jul 2025 pada 17.52
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_lpk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_absensi`
--

CREATE TABLE `tb_absensi` (
  `id_absen` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `keterangan` enum('Hadir','Izin','Sakit','Alpha') DEFAULT 'Hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_absensi`
--

INSERT INTO `tb_absensi` (`id_absen`, `id_pengguna`, `tanggal`, `jam_masuk`, `jam_pulang`, `keterangan`) VALUES
(1, 7, '2025-06-30', '17:17:01', '17:19:49', 'Sakit'),
(2, 7, '2006-01-21', '22:23:00', '00:00:00', 'Izin'),
(3, 8, '2025-06-30', '18:49:56', '18:49:58', 'Hadir'),
(4, 7, '2025-07-01', '05:28:57', '05:29:13', 'Hadir'),
(5, 8, '2025-07-01', NULL, NULL, 'Sakit'),
(6, 8, '2020-02-20', '09:00:00', '16:00:00', 'Hadir'),
(7, 8, '2025-07-26', '11:16:45', '11:16:48', 'Hadir'),
(8, 7, '2025-07-29', '16:36:09', '16:36:18', NULL),
(9, 8, '2025-07-29', '08:00:00', '15:21:46', NULL),
(10, 9, '2025-07-29', NULL, '14:55:52', 'Izin'),
(11, 10, '2025-07-29', '21:47:41', '16:48:54', NULL),
(12, 11, '2025-07-29', '08:00:00', '15:58:13', NULL),
(13, 12, '2025-07-29', '08:00:00', '16:09:35', NULL),
(14, 15, '2025-07-29', '08:00:00', '15:31:11', NULL),
(15, 18, '2025-07-29', '08:00:00', NULL, NULL),
(16, 6, '2025-07-29', '15:16:40', '15:16:49', 'Hadir'),
(17, 7, '2025-07-30', '00:00:00', '00:00:00', 'Hadir'),
(18, 9, '2025-07-30', '09:36:03', '09:36:09', 'Izin'),
(19, 11, '2025-07-30', '09:44:57', NULL, 'Hadir'),
(20, 10, '2025-07-30', '10:16:45', NULL, 'Izin'),
(21, 8, '2025-07-30', NULL, NULL, 'Alpha'),
(22, 12, '2025-07-30', NULL, NULL, 'Alpha'),
(23, 15, '2025-07-30', NULL, NULL, 'Alpha'),
(24, 18, '2025-07-30', NULL, NULL, 'Alpha'),
(25, 19, '2025-07-30', NULL, NULL, 'Alpha'),
(26, 20, '2025-07-30', NULL, NULL, 'Alpha'),
(27, 21, '2025-07-30', NULL, NULL, 'Alpha'),
(28, 22, '2025-07-30', NULL, NULL, 'Alpha'),
(29, 23, '2025-07-30', NULL, NULL, 'Alpha'),
(30, 24, '2025-07-30', NULL, NULL, 'Alpha'),
(31, 25, '2025-07-30', NULL, NULL, 'Alpha'),
(32, 26, '2025-07-30', NULL, NULL, 'Izin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_koreksi_absen`
--

CREATE TABLE `tb_koreksi_absen` (
  `id_koreksi` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_koreksi` datetime NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_koreksi_absen`
--

INSERT INTO `tb_koreksi_absen` (`id_koreksi`, `id_pengguna`, `tanggal`, `waktu_koreksi`, `alasan`, `status`) VALUES
(1, 8, '2025-07-29', '2025-07-29 15:22:22', 'lupa absen', 'Disetujui'),
(2, 18, '2025-07-29', '2025-07-29 15:45:58', 'lupa absen', 'Disetujui'),
(3, 11, '2025-07-29', '2025-07-29 20:57:30', 'maaf pak saya hadir cuman lupa', 'Disetujui'),
(4, 12, '2025-07-29', '2025-07-29 21:09:14', 'maaf', 'Disetujui'),
(5, 15, '2025-07-29', '2025-07-29 21:28:28', 'maaf', 'Disetujui'),
(6, 7, '2025-07-29', '2025-07-29 21:36:09', 'maap ya', 'Ditolak'),
(7, 10, '2025-07-29', '2025-07-29 21:47:41', 'aw', 'Disetujui'),
(8, 10, '2025-07-29', '2025-07-29 21:47:41', 'aw', 'Ditolak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pengajuan_izin`
--

CREATE TABLE `tb_pengajuan_izin` (
  `id_pengajuan` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jenis` enum('Izin','Sakit') DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `status` enum('Menunggu','Diterima','Ditolak') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_pengajuan_izin`
--

INSERT INTO `tb_pengajuan_izin` (`id_pengajuan`, `id_pengguna`, `tanggal`, `jenis`, `alasan`, `status`) VALUES
(1, 7, '2025-06-30', 'Sakit', 'saya ijin', 'Diterima'),
(2, 8, '2025-07-01', 'Sakit', 'nyong ra bisa teka', 'Diterima'),
(3, 8, '2025-07-26', 'Sakit', 'mbuh malas\r\n', 'Ditolak'),
(4, 9, '2025-07-29', 'Izin', 'aa', 'Diterima'),
(5, 7, '2025-07-30', 'Sakit', 'map', 'Diterima'),
(6, 9, '2025-07-30', 'Izin', 'maap', 'Diterima'),
(7, 10, '2025-07-30', 'Izin', 'coba', 'Diterima'),
(8, 26, '2025-07-30', 'Izin', 'Maap', 'Diterima');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pengguna`
--

CREATE TABLE `tb_pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','siswa') DEFAULT 'siswa',
  `tanggal_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_pengguna`
--

INSERT INTO `tb_pengguna` (`id_pengguna`, `nama_lengkap`, `username`, `email`, `password`, `role`, `tanggal_dibuat`) VALUES
(6, 'Admin LPK', 'admin', NULL, '$2y$10$dp0huTswJ/9xdfGqwDZWJeUX/CtmbPhOsOCuO5PdnwPqTv7vfZiJ6', 'admin', '2025-07-29 09:58:24'),
(7, 'Siswa LPK', 'siswa1', NULL, '$2y$10$AUvS4BRuDHEmG0oiy.NgHuf4HODY8tpg/xrq9GUACcI081sAvWF..', 'siswa', '2025-07-29 09:58:24'),
(8, 'zaki', 'zaki', 'muhammadbaehaqi12@gmail.com', '$2y$10$/oyP/MSJaBqJrw9QzmJjxe0NwNtcN.VoFCkDqPuTBUWqQWUEzK66G', 'siswa', '2025-07-29 09:58:24'),
(9, 'Muhammad Baehaqi', 'Haki', 'muhammadbaehaki13@gmail.com', '$2y$10$Rm4e1b32Z3oiaM/wqf2FXOW61dOISvAjzkyFSkhf8YmBRBRTbvKnS', 'siswa', '2025-07-29 09:58:24'),
(10, 'Nanda Muhammad Anshor', 'aan', 'aan12@gmail.com', '$2y$10$3Sze.BP8WHuUmguFj/xXNOBJy8vcbMKaDMRF4NyNG2IH0HUB.3p0G', 'siswa', '2025-07-29 09:58:24'),
(11, 'eka soni pradana', 'eka`', 'eka@gmail.com', '$2y$10$csiddjIaY8eHokeDAIGnku.SKz5aqiDK8bxU7j.q0ofc9UNsTcGWu', 'siswa', '2025-07-29 09:58:24'),
(12, 'Zamzami', 'Zami', 'zami@gmail.com', '$2y$10$9pcUTxMFKfn9YZHoSxkmKu6X1napOXC8ezesrEW1KLQc86N/DMSJq', 'siswa', '2025-07-29 09:58:24'),
(13, 'haki', 'haki12', 'xulo1@mailinator.com', '$2y$10$3Q2imd.L1u79k/ZU.iTy9uUSxH84i1BPBLEGKfP0FiY9jW.HSpdAK', 'admin', '2025-07-29 09:58:24'),
(14, 'haki', 'haki1', 'coba1@gmail.com', '$2y$10$NqYJ9BptL1PN2vakveflmOFdekKEINceTYWJo06c0rXMcLluh.SRO', 'admin', '2025-07-29 09:58:24'),
(15, 'dimas', 'dimas1', 'xulo@mailinator.com', '$2y$10$lIytytmKuYJAySUfFV5MnuWDaB7wRSrzPhbALxq1c7rEW8cVZKx8u', 'siswa', '2025-07-29 09:58:24'),
(16, 'dimas', 'dimas2', 'coba@gmail.com', '$2y$10$OkzF.UzodqFnP1PHBoLas.vGmHOnqFRt9QVxYa3eEO9tGo2roUPLu', 'admin', '2025-07-29 09:58:24'),
(18, 'anwar jaman', 'anwar1', 'anwar@gmail.com', '$2y$10$lmzcO9wK10hsamhfftdjKefM.xJPcsFWCwGBEAqgW6pNZxLeXv77W', 'siswa', '2025-07-29 09:58:24'),
(19, 'haki1', 'haki5', 'haki12@gmail.com', '$2y$10$uKSSGinda/dhQHwTsWPbce.MhTaFzenxasi9oesEQDohWp5S.Whha', 'siswa', '2025-07-29 09:58:24'),
(20, 'naful muzaki', 'naul', 'naul@gmail.com', '$2y$10$5nM01xZFqsGLXtOPo85Fnu4cw9DdmyAuQ4pq6DdfW7XXoMTqxFGTi', 'siswa', '2025-07-29 21:31:13'),
(21, 'aizul fikri', 'izul', 'izul@gmail.com', '$2y$10$Frihmi0xr1.h.AtgmKLyL.SQkdzFkSg9lNweyJYhvs770ym1x1g/y', 'siswa', '2025-07-29 22:14:58'),
(22, 'hikmatul hukamah', 'hikmah', 'hikmah@gmail.com', '$2y$10$2OEcrFdogFs8WnF58m0HzelkCW7Nso78lupzT89j4tMqhvObJHplu', 'siswa', '2025-07-29 22:15:19'),
(23, 'ustad zaki', 'zaky', 'zaky@gmail.com', '$2y$10$/23wEZw/xaihFODvvN4ugO57X4TBC49YodlffdMDMDek3uMmTxzzS', 'siswa', '2025-07-29 22:15:42'),
(24, 'farhan setiawan', 'farhan', 'farhan@gmail.com', '$2y$10$l4v2EiDgPRWrgFrajpmns.kdSRnpA/5pJ6/NUsEPTxuNXm5NwdvaK', 'siswa', '2025-07-29 22:16:20'),
(25, 'wendi setiawan', 'wendi', 'wendi@gmail.com', '$2y$10$yIHz45EZc0tjjA.AFdmZOeSx978tCpGdy6AYEWKnc9f72CkyydCWa', 'siswa', '2025-07-29 22:16:38'),
(26, 'agip setiawan', 'agip', 'agip@gmail.com', '$2y$10$fmpTlwRb86jqLDhJD5Ew3um6MAystEF546.govIrcEPBFnTnECDo.', 'siswa', '2025-07-29 22:16:55');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  ADD PRIMARY KEY (`id_absen`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `tb_koreksi_absen`
--
ALTER TABLE `tb_koreksi_absen`
  ADD PRIMARY KEY (`id_koreksi`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `tb_pengajuan_izin`
--
ALTER TABLE `tb_pengajuan_izin`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indeks untuk tabel `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  MODIFY `id_absen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `tb_koreksi_absen`
--
ALTER TABLE `tb_koreksi_absen`
  MODIFY `id_koreksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_pengajuan_izin`
--
ALTER TABLE `tb_pengajuan_izin`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  ADD CONSTRAINT `tb_absensi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `tb_pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `tb_koreksi_absen`
--
ALTER TABLE `tb_koreksi_absen`
  ADD CONSTRAINT `tb_koreksi_absen_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `tb_pengguna` (`id_pengguna`);

--
-- Ketidakleluasaan untuk tabel `tb_pengajuan_izin`
--
ALTER TABLE `tb_pengajuan_izin`
  ADD CONSTRAINT `tb_pengajuan_izin_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `tb_pengguna` (`id_pengguna`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
