<?php
// 1. KEAMANAN: Pastikan hanya user yang sudah login dan berperan sebagai 'mahasiswa' yang bisa akses halaman ini.
session_start();
require_once '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek role user
if ($_SESSION['role'] !== 'mahasiswa') {
    // Jika bukan mahasiswa, arahkan ke halaman login
    header("Location: ../login.php");
    exit();
}

// 2. KONEKSI & PENGAMBILAN DATA: Hubungkan ke database dan ambil data mahasiswa
try {
    // Ambil data mahasiswa berdasarkan id_mahasiswa dari session
    $sql_mahasiswa = "SELECT m.id, m.nama_lengkap, m.email, m.nim, m.jurusan, m.foto_profil
                      FROM mahasiswa m
                      WHERE m.id = (SELECT id_mahasiswa FROM users WHERE id = ?)";

    $stmt_mahasiswa = $pdo->prepare($sql_mahasiswa);
    $stmt_mahasiswa->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);

    // Jika data mahasiswa tidak ditemukan
    if (!$mahasiswa) {
        $error_message = "Data mahasiswa tidak ditemukan.";
    }

    // Query untuk mengambil semua project milik mahasiswa yang sedang login
    $sql_projects = "SELECT p.*, k.nama_kategori
                    FROM projects p
                    JOIN kategori_proyek k ON p.id_kategori = k.id
                    WHERE p.id_mahasiswa = ?
                    ORDER BY p.tanggal DESC";

    $stmt_projects = $pdo->prepare($sql_projects);
    $stmt_projects->execute([$mahasiswa['id']]);
    $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Terjadi kesalahan saat mengambil data: " . $e->getMessage();
    $projects = [];
}
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
            --accent-color: #55bddd;
            --accent-hover: #0097a7;
            --light-color: #ffffff;
            --text-light: #f8f9fa;
            --text-dark: #333333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            color: var(--text-dark);
            padding-top: 76px; /* Menghindari konten tertutup navbar */
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
            background: linear-gradient(rgba(0, 51, 102, 0.7), rgba(0, 31, 63, 0.7)), url('../bg-gedung.jpg') no-repeat center center/cover;
            color: var(--light-color);
            padding: 100px 0;
            text-align: center;
            margin-bottom: 30px;
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

        /* --- Profile Image --- */
        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 8px;
        }

        /* --- Responsive --- */
        @media (max-width: 768px) {
            .hero {
                padding: 60px 0;
            }

            .hero h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home_mhs.php">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home_mhs.php">Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <!-- Dropdown Profil -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php if (!empty($mahasiswa['foto_profil'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($mahasiswa['foto_profil']) ?>" class="profile-img" alt="Profile">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 me-1"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="profil_page.php"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="upload_project.php"><i class="bi bi-plus-circle me-2"></i>Tambah Proyek Baru</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Selamat datang, <span><?= htmlspecialchars($mahasiswa['nama_lengkap']); ?>!</span></h1>
            <p class="lead">Kelola dan tampilkan karya terbaikmu di sini.</p>
            <a href="upload_project.php" class="btn btn-lg btn-light mt-3">
                <i class="bi bi-plus-circle"></i> Tambah Proyek Baru
            </a>
        </div>
    </section>

    <!-- Main Content: Daftar Proyek -->
    <section class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Proyek Saya</h2>
            <div class="d-flex">
                <div class="input-group me-2">
                    <input type="text" class="form-control" placeholder="Cari proyek..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#" data-filter="all">Semua</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="terbaru">Terbaru</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="terlama">Terlama</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <div class="row" id="projectsContainer">
            <?php if (empty($projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. <a href="upload_project.php">Tambahkan proyek pertama Anda!</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4 project-item" data-category="<?= htmlspecialchars($project['nama_kategori']) ?>">
                        <div class="card project-card">
                            <?php if (!empty($project['gambar'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($project['gambar']) ?>" class="card-img-top" alt="Project Image">
                            <?php else: ?>
                                <img src="https://picsum.photos/seed/project<?= $project['id'] ?>/400/200.jpg" class="card-img-top" alt="Project Image">
                            <?php endif; ?>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fungsi pencarian
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const projectItems = document.querySelectorAll('.project-item');

            function filterProjects() {
                const searchTerm = searchInput.value.toLowerCase();

                projectItems.forEach(item => {
                    const title = item.querySelector('.card-title').textContent.toLowerCase();
                    const description = item.querySelector('.card-text').textContent.toLowerCase();

                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('keyup', filterProjects);
            searchBtn.addEventListener('click', filterProjects);

            // Fungsi filter dropdown
            const filterDropdown = document.querySelectorAll('#filterDropdown .dropdown-item');

            filterDropdown.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.getAttribute('data-filter');
                    const container = document.getElementById('projectsContainer');
                    const projects = Array.from(container.querySelectorAll('.project-item'));

                    if (filter === 'all') {
                        // Tampilkan semua proyek
                        projects.forEach(project => {
                            container.appendChild(project);
                        });
                    } else if (filter === 'terbaru') {
                        // Urutkan dari terbaru
                        projects.sort((a, b) => {
                            const dateA = new Date(a.querySelector('.card-text').textContent);
                            const dateB = new Date(b.querySelector('.card-text').textContent);
                            return dateB - dateA;
                        });

                        projects.forEach(project => {
                            container.appendChild(project);
                        });
                    } else if (filter === 'terlama') {
                        // Urutkan dari terlama
                        projects.sort((a, b) => {
                            const dateA = new Date(a.querySelector('.card-text').textContent);
                            const dateB = new Date(b.querySelector('.card-text').textContent);
                            return dateA - dateB;
                        });

                        projects.forEach(project => {
                            container.appendChild(project);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>