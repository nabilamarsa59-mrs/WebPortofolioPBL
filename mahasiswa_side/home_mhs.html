<?php
// 1. KEAMANAN: Pastikan hanya user yang sudah login dan berperan sebagai 'mahasiswa' yang bisa akses halaman ini.
require_once '../auth_check.php';
if ($_SESSION['role'] !== 'mahasiswa') {
    // Jika bukan mahasiswa, arahkan ke halaman login
    header("Location: ../login.php");
    exit();
}

// 2. KONEKSI & PENGAMBILAN DATA: Hubungkan ke database dan ambil data proyek milik mahasiswa ini.
require_once '../koneksi.php';

// Ambil ID user dari session
 $user_id = $_SESSION['user_id'];
 $nama_mahasiswa = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Mahasiswa';

// Query untuk mengambil semua project milik mahasiswa yang sedang login
 $sql = "SELECT p.*, k.nama_kategori 
        FROM projects p
        JOIN kategori_proyek k ON p.id_kategori = k.id
        WHERE p.id_mahasiswa = (SELECT id_mahasiswa FROM users WHERE id = ?)
        ORDER BY p.tanggal DESC";

 $projects = []; // Inisialisasi array di luar, agar selalu ada
 $error_message = ''; // Variabel untuk menampung pesan error

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Jangan hentikan skrip, simpan pesan error ke variabel
    $error_message = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    // $projects sudah kosong dari awal, jadi tidak perlu diisi lagi
}

// Nanti di bagian HTML, Anda bisa cek: // 
if ($error_message) { echo '<div class="alert alert-danger">' . $error_message . '</div>'; }
if (empty($projects)) { echo '<p>Tidak ada proyek ditemukan.</p>'; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPiece - Dashboard Mahasiswa</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #001F3F;
            --accent-color: #00bcd4;
            --accent-hover: #0097a7;
            --light-color: #ffffff;
            --text-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
<<<<<<< HEAD:mahasiswa_side/home_mhs.html
            background: url('../bg-gedung.jpg') no-repeat center center/cover fixed;
            color: var(--text-light);
            padding-top: 76px; /* Menghindari konten tertutup navbar */
=======
            color: #333; /* Ubah agar teks konten lebih mudah dibaca */
            padding-top: 76px;
>>>>>>> c2fae3ab9e64d9fc249bc19b89dc7ed455feef29:mahasiswa_side/home_mhs.php
        }

        /* --- Custom Navbar Styles --- */
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand, .navbar-nav .nav-link, .dropdown-item {
            color: var(--light-color) !important;
        }

        .navbar-nav .nav-link:hover, .dropdown-item:hover {
            color: var(--accent-color) !important;
        }

        .dropdown-menu {
            background-color: var(--secondary-color);
            border: none;
        }

        .search-form {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 5px 10px;
        }
        
        .search-input {
            border: none;
            outline: none;
            background: transparent;
            color: var(--text-light);
            padding: 5px 10px;
            width: 160px;
            font-size: 0.9rem;
        }

        /* --- Hero Section --- */
        .hero {
            background: linear-gradient(rgba(0, 30, 100, 0.7), rgba(0, 30, 100, 0.7)), url('https://picsum.photos/seed/student-dashboard/1920/800.jpg') no-repeat center center/cover;
            color: var(--light-color);
            padding: 100px 0;
            text-align: center;
        }

        /* --- Project Card Styles --- */
        .project-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            height: 100%;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .btn-action {
            border-radius: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home_mahasiswa.php">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home_mahasiswa.php">Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Dropdown Profil -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($nama_mahasiswa) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
<<<<<<< HEAD:mahasiswa_side/home_mhs.html
                            <li><a class="dropdown-item" href="profil_page.php">Profil</a></li>
                            <li><a class="dropdown-item" href="upload_project.php">Proyek</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="landing_page.php">Logout</a></li>
=======
                            <li><a class="dropdown-item" href="profil_mahasiswa.php">Profil Saya</a></li>
                            <li><a class="dropdown-item" href="tambah_project.php">Tambah Proyek Baru</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <!-- 3. PERBAIKAN: Link logout harus mengarah ke file logout.php -->
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
>>>>>>> c2fae3ab9e64d9fc249bc19b89dc7ed455feef29:mahasiswa_side/home_mhs.php
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Selamat datang, <span><?= htmlspecialchars($nama_mahasiswa); ?>!</span></h1>
            <p class="lead">Kelola dan tampilkan karya terbaikmu di sini.</p>
            <a href="tambah_project.php" class="btn btn-lg btn-light mt-3">
                <i class="bi bi-plus-circle"></i> Tambah Proyek Baru
            </a>
        </div>
    </section>

    <!-- Main Content: Daftar Proyek -->
    <section class="container my-5">
        <h2 class="mb-4">Proyek Saya</h2>
        
        <div class="row">
            <?php if (empty($projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. <a href="tambah_project.php">Tambahkan proyek pertama Anda!</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card project-card">
                            <img src="../uploads/<?= htmlspecialchars($project['gambar'] ?? 'default_project.jpg'); ?>" class="card-img-top" alt="Project Image">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($project['judul']); ?></h5>
                                <p class="card-text text-muted small">Kategori: <?= htmlspecialchars($project['nama_kategori']); ?></p>
                                <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="edit_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-primary btn-action">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="proses_hapus_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>