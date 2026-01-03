-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 07:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_portofolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nidn` varchar(20) DEFAULT NULL,
  `bidang_keahlian` text DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL COMMENT 'Path foto profil dosen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `nama_lengkap`, `email`, `nidn`, `bidang_keahlian`, `foto_profil`) VALUES
(1, 'Yeni Rokhayati, S.Si., M.Sc\r\n', 'yeni@polibatam.ac.id', '0123456789', 'Teknik Informatika', 'dosen_1_1766551504.jpg'),
(2, 'Ir. Dwi Ely Kurniawan, S.Pd., M.Kom', 'dwialikhs@polibatam.ac.id', '112094', 'Teknologi Rekayasa Multimedia', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_proyek`
--

CREATE TABLE `kategori_proyek` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `jurusan` varchar(100) DEFAULT NULL COMMENT 'Jurusan yang memiliki kategori ini'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_proyek`
--

INSERT INTO `kategori_proyek` (`id`, `nama_kategori`, `jurusan`) VALUES
(63, '2D Animation Project', 'Teknik Informatika'),
(21, '3D Asset', 'Teknik Informatika'),
(27, '3D Asset untuk Film dan Game / Simulasi', 'Teknik Informatika'),
(60, '3D Game Project', 'Teknik Informatika'),
(228, 'Administrasi dan Perkantoran', 'Manajemen dan Bisnis'),
(239, 'AIoT', 'Teknik Elektro'),
(242, 'Alat checker', 'Teknik Elektro'),
(249, 'Alat monitoring Energi Baru Terbarukan (EBT)', 'Teknik Elektro'),
(252, 'Alat ukur', 'Teknik Elektro'),
(29, 'Animasi Stopmotion', 'Teknik Informatika'),
(28, 'Aplikasi', 'Teknik Informatika'),
(32, 'Aplikasi AR', 'Teknik Informatika'),
(45, 'Aplikasi Cyber Security', 'Teknik Informatika'),
(276, 'Aplikasi dan hardware', 'Teknik Elektro'),
(53, 'Aplikasi dan Tools Cyber', 'Teknik Informatika'),
(20, 'Aplikasi Geomatika', 'Teknik Informatika'),
(26, 'Aplikasi IoT', 'Teknik Informatika'),
(223, 'Aplikasi Keuangan Berbasis Cloud', 'Manajemen dan Bisnis'),
(24, 'Aplikasi Mobile', 'Teknik Informatika'),
(19, 'Aplikasi Web', 'Teknik Informatika'),
(34, 'Application and Network Security Engineering', 'Teknik Informatika'),
(50, 'Artificial Intelligence', 'Teknik Informatika'),
(36, 'Augmented Reality', 'Teknik Informatika'),
(232, 'Automation', 'Teknik Elektro'),
(181, 'Baru', 'Manajemen dan Bisnis'),
(206, 'Bidang Akuntansi', 'Manajemen dan Bisnis'),
(267, 'Clean Energi', 'Teknik Elektro'),
(39, 'Concept Art', 'Teknik Informatika'),
(256, 'Data Logger', 'Teknik Elektro'),
(44, 'Data Mining', 'Teknik Informatika'),
(282, 'Desain', 'Teknik Mesin'),
(290, 'Desain dan Fabrikasi', 'Teknik Mesin'),
(298, 'Desain dan Fabrikasi Kapal', 'Teknik Mesin'),
(30, 'Desain dan Foto Produk', 'Teknik Informatika'),
(54, 'Desain Grafis', 'Teknik Informatika'),
(283, 'Desain-CAD', 'Teknik Mesin'),
(277, 'Desain-Fabrikasi', 'Teknik Elektro'),
(284, 'Desain-Fabrikasi', 'Teknik Mesin'),
(288, 'Desain-Fabrikasi (tahap pengembangan)', 'Teknik Mesin'),
(287, 'Desain/Pembuatan Produk', 'Teknik Mesin'),
(46, 'Design Project', 'Teknik Informatika'),
(272, 'Drone', 'Teknik Elektro'),
(305, 'Eksternal (Prodi Teknik Mesin)', 'Teknik Mesin'),
(265, 'Electronics Circuit Drafting/Design [Prototyping]', 'Teknik Elektro'),
(278, 'Elektronik Produk', 'Teknik Elektro'),
(186, 'English Corner', 'Manajemen dan Bisnis'),
(209, 'ERP & Pengembangan Aplikasi', 'Manajemen dan Bisnis'),
(37, 'ERP & Pengembangan Aplikasi', 'Teknik Informatika'),
(62, 'Experimental Animation', 'Teknik Informatika'),
(285, 'Fabrikasi', 'Teknik Mesin'),
(55, 'Game', 'Teknik Informatika'),
(51, 'Game Berbasis Perangkat Keras', 'Teknik Informatika'),
(52, 'Game Conceptual Project', 'Teknik Informatika'),
(215, 'GEDSI', 'Manajemen dan Bisnis'),
(35, 'Geomatika (Surveying and Mapping)', 'Teknik Informatika'),
(258, 'Hardware dan aplikasi', 'Teknik Elektro'),
(270, 'IC Packaging', 'Teknik Elektro'),
(22, 'Ilustrasi', 'Teknik Informatika'),
(193, 'Implementasi hasil penelitian', 'Manajemen dan Bisnis'),
(289, 'Injection Molding', 'Teknik Mesin'),
(210, 'Inkubasi Bisnis dan Kewirausahaan', 'Manajemen dan Bisnis'),
(263, 'Instalasi Listrik', 'Teknik Elektro'),
(257, 'Instrumentasi', 'Teknik Elektro'),
(205, 'Internal', 'Manajemen dan Bisnis'),
(203, 'Internal & Lanjutan', 'Manajemen dan Bisnis'),
(315, 'Internal Prodi Metalurgi (Desain)', 'Teknik Mesin'),
(314, 'Internal Prodi Metalurgi (Pengujian)', 'Teknik Mesin'),
(306, 'Internal Prodi Pengelasan (Desain)', 'Teknik Mesin'),
(303, 'Internal Prodi Pengelasan (DT dan NDT)', 'Teknik Mesin'),
(304, 'Internal Prodi Pengelasan (Fabrikasi)', 'Teknik Mesin'),
(307, 'Internal Prodi Pengelasan (Inspeksi)', 'Teknik Mesin'),
(301, 'Internal Prodi Teknik Mesin (Desain)', 'Teknik Mesin'),
(297, 'Internal Prodi Teknik Mesin (Fabrikasi)', 'Teknik Mesin'),
(313, 'Internal Prodi Teknik Mesin (Pengukuran/Inspeksi)', 'Teknik Mesin'),
(296, 'Internal Prodi Teknik Mesin (Proses Pemesinan)', 'Teknik Mesin'),
(238, 'IoT', 'Teknik Elektro'),
(264, 'IoT System', 'Teknik Elektro'),
(196, 'Jasa', 'Manajemen dan Bisnis'),
(226, 'Kebijakan Akuntansi', 'Manajemen dan Bisnis'),
(188, 'Keselamatan dan Kesehatan Kerja Lingkungan', 'Manajemen dan Bisnis'),
(207, 'Keuangan, Pasar Modal dan Pasar Uang', 'Manajemen dan Bisnis'),
(185, 'Komersialisasi', 'Manajemen dan Bisnis'),
(23, 'Komik', 'Teknik Informatika'),
(222, 'Kompetisi', 'Manajemen dan Bisnis'),
(199, 'Kompetisi Mahasiswa', 'Manajemen dan Bisnis'),
(40, 'Konseptual Advertising Project', 'Teknik Informatika'),
(259, 'Kontes Robot Indonesia', 'Teknik Elektro'),
(194, 'Lanjutan', 'Manajemen dan Bisnis'),
(229, 'Laporan Audit Keuangan dan Pelaporan Keuangan', 'Manajemen dan Bisnis'),
(230, 'Laporan Evaluasi Kinerja dan Audit Keuangan', 'Manajemen dan Bisnis'),
(64, 'Lighting, Rendering, & Compositing', 'Teknik Informatika'),
(208, 'Logistik dan Supply Chain', 'Manajemen dan Bisnis'),
(177, 'Lomba', 'Manajemen dan Bisnis'),
(308, 'Lomba (Prodi Kapal)', 'Teknik Mesin'),
(184, 'Lomba/Kompetisi', 'Manajemen dan Bisnis'),
(260, 'Manufaktur Elektronik', 'Teknik Elektro'),
(253, 'Manufaktur Elektronika', 'Teknik Elektro'),
(310, 'MBKM Perkapalan', 'Teknik Mesin'),
(279, 'Mesin', 'Teknik Mesin'),
(202, 'MF – Komersialisasi Produk', 'Manajemen dan Bisnis'),
(201, 'Modul dan Pendampingan usaha', 'Manajemen dan Bisnis'),
(192, 'Modul dan Webinar', 'Manajemen dan Bisnis'),
(190, 'Modul Pengelolaan dan Pendampingan UMKM', 'Manajemen dan Bisnis'),
(56, 'Motion Graphic', 'Teknik Informatika'),
(58, 'On-Premise Data Center Administration', 'Teknik Informatika'),
(240, 'PBL', 'Teknik Elektro'),
(195, 'PBL Internal', 'Manajemen dan Bisnis'),
(262, 'PBL Internal Kompetisi', 'Teknik Elektro'),
(248, 'PBL internal Robotika', 'Teknik Elektro'),
(293, 'PBL Lomba', 'Teknik Mesin'),
(191, 'PBL – BUSINESS SERVICES', 'Manajemen dan Bisnis'),
(271, 'PCB Manufacturing', 'Teknik Elektro'),
(212, 'Pemasaran dan Penjualan', 'Manajemen dan Bisnis'),
(233, 'Pembuatan alat dan sistem', 'Teknik Elektro'),
(178, 'Pembuatan Buku Praktikum', 'Manajemen dan Bisnis'),
(281, 'Pembuatan desain', 'Teknik Mesin'),
(211, 'Pembuatan Laporan', 'Manajemen dan Bisnis'),
(217, 'Pembuatan Laporan Keuangan', 'Manajemen dan Bisnis'),
(280, 'Pembuatan Mesin', 'Teknik Mesin'),
(213, 'Pembuatan Modul', 'Manajemen dan Bisnis'),
(224, 'Pendampingan Literasi & Perencanaan Keuangan', 'Manajemen dan Bisnis'),
(220, 'Pendampingan Media & Promosi Produk', 'Manajemen dan Bisnis'),
(221, 'Pendampingan Perpajakan Pajak', 'Manajemen dan Bisnis'),
(187, 'Pendampingan Proses Produk Halal UMKM', 'Manajemen dan Bisnis'),
(219, 'Pendampingan UMKM', 'Manajemen dan Bisnis'),
(218, 'Pendampingan UMKM & APINDO MERDEKA', 'Manajemen dan Bisnis'),
(200, 'Penelitian', 'Manajemen dan Bisnis'),
(316, 'Penelitian', 'Teknik Mesin'),
(189, 'Pengabdian', 'Manajemen dan Bisnis'),
(309, 'Pengabdian (Prodi Teknik Mesin)', 'Teknik Mesin'),
(312, 'Pengabdian kepada Masyarakat', 'Teknik Mesin'),
(214, 'Pengembangan Bisnis – Kemaritiman dan Kelautan', 'Manajemen dan Bisnis'),
(227, 'Pengembangan CoE Polibatam', 'Manajemen dan Bisnis'),
(59, 'Pengembangan Jaringan Komputer', 'Teknik Informatika'),
(255, 'Pengembangan Perpustakaan', 'Teknik Elektro'),
(198, 'Penyusunan Tarif BLU Polibatam', 'Manajemen dan Bisnis'),
(291, 'Perencanaan dan fabrikasi Stand TV (Custom)', 'Teknik Mesin'),
(179, 'Pergudangan dan Penerimaan, Pengantar Logistik K3', 'Manajemen dan Bisnis'),
(302, 'Perlombaan (Prodi Teknik Mesin)', 'Teknik Mesin'),
(286, 'Persiapan lomba', 'Teknik Mesin'),
(292, 'Persiapan Magang Perkapalan', 'Teknik Mesin'),
(225, 'PKM dan Kegiatan Masyarakat', 'Manajemen dan Bisnis'),
(294, 'Preliminary Ship Design', 'Teknik Mesin'),
(299, 'Preliminary Ship Design (Prodi KP)', 'Teknik Mesin'),
(241, 'Product', 'Teknik Elektro'),
(275, 'Product Manufacturing', 'Teknik Elektro'),
(243, 'Product tanirier', 'Teknik Elektro'),
(247, 'Produk', 'Teknik Elektro'),
(204, 'Program Kreativitas Mahasiswa', 'Manajemen dan Bisnis'),
(311, 'Project Based Learning (PBL) Internal', 'Teknik Mesin'),
(197, 'Proses Bisnis', 'Manajemen dan Bisnis'),
(261, 'Prototype Elektronika', 'Teknik Elektro'),
(43, 'Proyek Industri', 'Teknik Informatika'),
(254, 'Proyek Internal', 'Teknik Elektro'),
(25, 'Rendering Project', 'Teknik Informatika'),
(268, 'Reverse Engineering Electronics Circuit', 'Teknik Elektro'),
(236, 'Robot', 'Teknik Elektro'),
(231, 'Robotika', 'Teknik Elektro'),
(48, 'Security Operation Center Development', 'Teknik Informatika'),
(47, 'Servel Tereistris dan atau Kartografis', 'Teknik Informatika'),
(295, 'Ship Basic and Detail Design', 'Teknik Mesin'),
(300, 'Ship Basic and Detail Design (Prodi KP)', 'Teknik Mesin'),
(65, 'SIG – Penginderaan Jauh dan Fotogrametri', 'Teknik Informatika'),
(251, 'Simulasi', 'Teknik Elektro'),
(245, 'Sistem Monitoring', 'Teknik Elektro'),
(234, 'Sistem otomasi', 'Teknik Elektro'),
(273, 'Sistem Pengukuran', 'Teknik Elektro'),
(237, 'Sistem Vision', 'Teknik Elektro'),
(269, 'SMT / PCB Assembly', 'Teknik Elektro'),
(216, 'SOP/Bisnis Proses', 'Manajemen dan Bisnis'),
(183, 'Studi Kelayakan Bisnis', 'Manajemen dan Bisnis'),
(182, 'Studi Komparatif', 'Manajemen dan Bisnis'),
(57, 'Survei Hidrografi dan atau Kewilayahan', 'Teknik Informatika'),
(235, 'System Automation', 'Teknik Elektro'),
(274, 'Teknologi Tepat Guna', 'Teknik Elektro'),
(250, 'Trainer Kit', 'Teknik Elektro'),
(180, 'Usulan Baru', 'Manajemen dan Bisnis'),
(244, 'Usulan Lanjutan', 'Teknik Elektro'),
(176, 'Video', 'Manajemen dan Bisnis'),
(31, 'Video', 'Teknik Informatika'),
(49, 'Video Live-Action', 'Teknik Informatika'),
(38, 'Videografi', 'Teknik Informatika'),
(33, 'Virtual Reality', 'Teknik Informatika'),
(266, 'Vision Inspection', 'Teknik Elektro'),
(246, 'Vision System', 'Teknik Elektro'),
(61, 'Visual Branding', 'Teknik Informatika'),
(42, 'Vulnerability Assessment dan Penetration Testing', 'Teknik Informatika'),
(41, 'Well-Architect', 'Teknik Informatika');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL COMMENT 'Path foto profil mahasiswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `nama_lengkap`, `email`, `nim`, `jurusan`, `foto_profil`) VALUES
(3, 'Meysa Ramelia Putri', 'meysaramelia@gmail.com', '3312501018', 'Teknik Informatika', 'profil_3312501018_6957441f49dea.jpg'),
(4, 'Irenessa Rosidin', 'irenessarosidin62@gmail.com', '3312501017', 'Teknik Mesin', 'profil_3312501017_695742e9ad0ad.jpg'),
(5, 'Nabila Marsa', 'nabilamarsa59@gmail.com', '3312501016', 'Manajemen dan Bisnis', 'profil_3312501016_6957433c79774.png'),
(6, 'Jastin Aliansyah', 'jastinali@gmail.com', '3312501001', 'Teknik Elektro', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `penilaian`
--

CREATE TABLE `penilaian` (
  `id` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `nilai` varchar(5) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal_dinilai` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penilaian`
--

INSERT INTO `penilaian` (`id`, `id_project`, `id_dosen`, `nilai`, `komentar`, `tanggal_dinilai`) VALUES
(8, 16, 1, 'A', 'bagus', '2026-01-02 04:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` int(11) DEFAULT NULL COMMENT 'ID kategori proyek (foreign key)',
  `tanggal` date DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `link_demo` varchar(255) DEFAULT NULL,
  `link_source` varchar(255) DEFAULT NULL,
  `id_mahasiswa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `judul`, `deskripsi`, `kategori`, `tanggal`, `gambar`, `link_demo`, `link_source`, `id_mahasiswa`) VALUES
(15, 'Stand Jobsheet Universal untuk Mesin Bubut dan Milling (Dengan Penambahan Magnet, dll)', 'Rancang bangun Stand Jobsheet Universal untuk mesin bubut dan milling dirancang untuk memudahkan akses dan kenyamanan operator dalam melihat jobsheet saat bekerja. Dilengkapi magnet untuk menempelkan jobsheet pada posisi optimal serta dudukan fleksibel, stand ini memberikan stabilitas dan dapat disesuaikan untuk berbagai sudut pandang sesuai kebutuhan operator.', 297, '2026-01-01', 'project_695743064eb5c2.88609795.png', 'https://youtu.be/FVhrlbOkdhg', NULL, 4),
(16, 'WEB PORTOFOLIO PROJEK PBL', 'Web Portofolio Projek PBL adalah suatu projeck based learning yang berbasis sistem digital yaitu website. Sistem ini akan digunakan untuk menampilkan hasil karya mahasiswa. Sebelum sistem ini dibuat hasil proyek mahasiswa sering kali tidak terdokumentasi dengan baik sehingga sulit diakses dan kurang dikenal. Oleh karena itu dibuatlah Web Portofolio oleh tim PBL IF-PAGI1A-5 sebagai media digital untuk menyimpan dan menampilkan hasil karya mahasiswa secara menarik dan terstruktur. ', 19, '2026-01-01', 'project_6956855980b231.82098100.jpg', 'https://youtu.be/RUr1A-U-9dQ?si=Ov3pCpndChQ3dqMK', NULL, 3),
(17, 'Pengelolaan Gudang dan Persediaan Minimarket Koperasi Polibatam', 'Proyek ini dilaksanakan dengan menerapkan pendekatan CDIO dengan tujuan untuk meningkatkan tata letak gudang dan manajemen persediaan pada Minimarket Koperasi Polibatam. Mata kuliah: CDIO LPI Sem 2', 216, '2026-01-01', 'project_6957435ae4c810.25922443.png', 'https://youtu.be/J8qV6d-tLh8?si=FrGkbP1ACOMzxj_P', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('dosen','mahasiswa') NOT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `id_mahasiswa` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `id_dosen`, `id_mahasiswa`) VALUES
(1, 'yeni', 'yeni2025', 'dosen', 1, NULL),
(3, 'meysa018', 'meysa2025', 'mahasiswa', NULL, 3),
(4, 'irenessa', 'irene2025', 'mahasiswa', NULL, 4),
(5, 'nabila', 'nabila2025', 'mahasiswa', NULL, 5),
(6, 'jastin', 'jastin2025', 'mahasiswa', NULL, 6),
(7, 'Dwi', 'Dwi2025', 'dosen', 2, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nidn` (`nidn`);

--
-- Indexes for table `kategori_proyek`
--
ALTER TABLE `kategori_proyek`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kategori_jurusan` (`nama_kategori`,`jurusan`),
  ADD KEY `idx_kategori_nama` (`nama_kategori`),
  ADD KEY `idx_kategori_jurusan` (`jurusan`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD KEY `idx_mahasiswa_nim` (`nim`),
  ADD KEY `idx_mahasiswa_jurusan` (`jurusan`);

--
-- Indexes for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_project` (`id_project`),
  ADD KEY `id_dosen` (`id_dosen`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `idx_projects_mahasiswa` (`id_mahasiswa`),
  ADD KEY `idx_projects_tanggal` (`tanggal`),
  ADD KEY `idx_projects_kategori` (`kategori`),
  ADD KEY `fk_projects_kategori` (`kategori`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_dosen` (`id_dosen`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kategori_proyek`
--
ALTER TABLE `kategori_proyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_kategori` FOREIGN KEY (`kategori`) REFERENCES `kategori_proyek` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
