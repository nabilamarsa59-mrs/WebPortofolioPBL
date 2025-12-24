<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

try {
    $sql_mahasiswa = "SELECT m.id, m.nama_lengkap, m.foto_profil, m.nim, m.jurusan
                          FROM users u
                          JOIN mahasiswa m ON u.id_mahasiswa = m.id
                          WHERE u.id = ?";
    $stmt_mahasiswa = $pdo->prepare($sql_mahasiswa);
    $stmt_mahasiswa->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);

    if (!$mahasiswa) {
        die("Data mahasiswa tidak ditemukan untuk user yang login.");
    }

    // Get MY projects only (existing functionality - NOT CHANGED)
    $sql_projects = "SELECT p.id, p.judul, p.deskripsi, k.nama_kategori, p.gambar, p.link_demo, p.tanggal
                FROM projects p
                LEFT JOIN kategori_proyek k ON p.kategori = k.id
                WHERE p.id_mahasiswa = ?
                ORDER BY p.tanggal DESC";

    $stmt_projects = $pdo->prepare($sql_projects);
    $stmt_projects->execute([$mahasiswa['id']]);
    $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

    // Get all categories for filter dropdown
    $sql_kategori = "SELECT * FROM kategori_proyek ORDER BY nama_kategori ASC";
    $stmt_kategori = $pdo->query($sql_kategori);
    $categories = $stmt_kategori->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPiece - Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #001F3F;
            --accent-color: #55bddd;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            color: #333;
            padding-top: 76px;
        }

        /* Existing styles - NOT CHANGED */
        .navbar {
            background: #00003c !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            padding-left: 100px;
            padding: 0.75rem 1rem;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            padding-left: 100px;
        }

        .navbar-nav {
            align-items: center;
            padding-right: 50px;
            padding-right: 50px;
        }

        .navbar-nav .nav-item {
            margin-left: 15px;
            margin-left: 15px;
        }

        .navbar-nav .nav-item:first-child {
            margin-left: 0;
            margin-left: 0;
        }

        .navbar-brand,
        .navbar-nav .nav-link,
        .dropdown-item {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover,
        .dropdown-item:hover {
            color: var(--accent-color) !important;
        }

        .dropdown-item {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover,

        .dropdown-item:hover {
            color: var(--accent-color) !important;
        }

        .dropdown-menu {
            background-color: var(--secondary-color);
            border: none;
        }

        .hero {
            padding-top: 80px;
            height: 100vh;
            background: linear-gradient(rgba(0, 30, 100, 0.5), rgba(0, 30, 100, 0.5)), url('../bg-gedung.jpg') no-repeat center center/cover;
            color: #fff;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-content span {
            color: #55bddd;
        }

        .hero-content p {
            font-size: 1.5rem;
        }

        .project-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 8px;
        }

        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin-top: 80x;
            width: 100%;
        }

        /* ============================================ */
        /* NEW STYLES - Search & Filter (ADDITION ONLY) */
        /* ============================================ */

        /* Tab Navigation */
        .view-tabs {
            margin: 2rem 0;
            border-bottom: 2px solid #e0e0e0;
        }

        .view-tabs .nav-link {
            color: #666;
            font-weight: 600;
            padding: 1rem 2rem;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
            cursor: pointer;
        }

        .view-tabs .nav-link:hover {
            color: var(--primary-color);
        }

        .view-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }

        /* Search & Filter Section */
        .search-filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .search-box {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .search-box input {
            padding-left: 45px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem 0.75rem 45px;
            font-size: 1rem;
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.1);
            outline: none;
        }

        .filter-dropdown {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            cursor: pointer;
        }

        .filter-dropdown:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.1);
            outline: none;
        }

        /* Project Card Enhancement */
        .project-card .mahasiswa-info {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .project-card .mahasiswa-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .project-card .mahasiswa-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--primary-color);
            margin: 0;
        }

        .project-card .mahasiswa-details {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        /* Loading & Error States */
        .loading-spinner {
            text-align: center;
            padding: 2rem;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .no-results i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        /* ============================================ */
        /* RESPONSIVE DESIGN - NEW ADDITION */
        /* ============================================ */

        /* Extra Small Devices (phones, 576px and down) */
        @media (max-width: 575.98px) {
            body {
                padding-top: 65px;
            }

            .navbar-brand {
                font-size: 1.2rem;
                padding-left: 1rem;
            }

            .hero h1 {
                font-size: 2rem !important;
            }

            .hero p {
                font-size: 1rem !important;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .view-tabs .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            .search-filter-section {
                padding: 1rem;
            }

            .search-box input,
            .filter-dropdown {
                font-size: 0.9rem;
            }

            .card-img-top {
                height: 150px;
            }

            .project-card .card-body {
                padding: 1rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.35rem 0.75rem;
            }
        }

        /* Small Devices (landscape phones, 576px and up) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .hero-content h1 {
                font-size: 3rem;
            }

            .hero-content p {
                font-size: 1.2rem;
            }
        }

        /* Medium Devices (tablets, 768px and up) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .hero-content h1 {
                font-size: 3.2rem;
            }
        }

        /* Large Devices (desktops, 992px and up) */
        @media (min-width: 992px) {
            .hero-content h1 {
                font-size: 3.5rem;
            }

            .search-filter-section .row {
                align-items: end;
            }
        }

        /* Extra Large Devices (large desktops, 1200px and up) */
        @media (min-width: 1200px) {
            .hero-content h1 {
                font-size: 4rem;
            }
        }

        /* Fix navbar collapse on mobile */
        @media (max-width: 991.98px) {
            .navbar-nav {
                padding-right: 0;
                margin-top: 1rem;
            }

            .navbar-nav .nav-item {
                margin-left: 0;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home_mhs.php">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                            role="button" data-bs-toggle="dropdown">
                            <?php if (!empty($mahasiswa['foto_profil'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($mahasiswa['foto_profil']) ?>?t=<?= time() ?>"
                                    class="profile-img" alt="Profile">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 me-1"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profil_page.php"><i class="bi bi-person me-2"></i>Profil
                                    Saya</a></li>
                            <li><a class="dropdown-item" href="upload_project.php"><i
                                        class="bi bi-plus-circle me-2"></i>Tambah Proyek Baru</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../logout.php"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="hero-content">
            <h1>Selamat datang, <span><?= htmlspecialchars($mahasiswa['nama_lengkap']); ?>!</span></h1>
            <p>Kelola dan tampilkan karya terbaikmu di sini.</p>
            <a href="upload_project.php" class="btn btn-lg btn-light mt-3"><i class="bi bi-plus-circle"></i> Tambah
                Proyek Baru</a>
        </div>
    </section>

    <!-- Main Content -->
    <section class="container my-5">
        <!-- NEW: Tab Navigation -->
        <ul class="nav nav-pills view-tabs" id="viewTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-projects" data-bs-toggle="pill"
                    data-bs-target="#projects-section" type="button" role="tab" aria-controls="projects-section"
                    aria-selected="true">
                    <i class="bi bi-folder me-2"></i>Proyek Saya
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-explore" data-bs-toggle="pill" data-bs-target="#explore-section"
                    type="button" role="tab" aria-controls="explore-section" aria-selected="false">
                    <i class="bi bi-globe me-2"></i>Jelajah Proyek Mahasiswa Lain
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="viewTabContent">
            <!-- Proyek Saya Tab (Original - NOT CHANGED) -->
            <div class="tab-pane fade show active" id="projects-section" role="tabpanel" aria-labelledby="tab-projects">
                <h2 class="mb-4">Proyek Saya</h2>
                <div class="row">
                    <?php if (empty($projects)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center" role="alert">
                                <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. <a
                                    href="upload_project.php">Tambahkan
                                    proyek pertama Anda!</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card project-card">
                                    <?php
                                    $project_image = '../uploads/default-project.png';
                                    if (!empty($project['gambar'])) {
                                        $project_image = '../uploads/' . $project['gambar'];
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($project_image) ?>?t=<?= time() ?>" class="card-img-top"
                                        alt="Project Image"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect fill=%22%23eeeeee%22 width=%22400%22 height=%22200%22/%3E%3Ctext fill=%22%23999999%22 font-family=%22Arial%22 font-size=%2218%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ETidak Ada Gambar%3C/text%3E%3C/svg%3E'">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($project['judul']); ?></h5>
                                        <p class="card-text"><small class="text-muted">Kategori:
                                                <?= htmlspecialchars($project['nama_kategori'] ?? 'Tidak ada kategori'); ?></small>
                                        </p>
                                        <p class="card-text">
                                            <?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>

                                        <!-- Tampilkan tombol video hanya jika ada linknya -->
                                        <?php if (!empty($project['link_demo'])): ?>
                                            <a href="<?= htmlspecialchars($project['link_demo']) ?>" target="_blank"
                                                class="btn btn-sm btn-danger mb-2">
                                                <i class="bi bi-youtube"></i> Lihat Video
                                            </a>
                                        <?php endif; ?>

                                        <div class="mt-auto d-flex justify-content-between">
                                            <a href="edit_project.php?id=<?= $project['id']; ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="proses_hapus_project.php?id=<?= $project['id']; ?>"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- NEW: Jelajah Proyek Mahasiswa Lain Tab -->
            <div class="tab-pane fade" id="explore-section" role="tabpanel" aria-labelledby="tab-explore">
                <h2 class="mb-4">Jelajah Proyek Mahasiswa Lain</h2>

                <!-- NEW: Search & Filter Section -->
                <div class="search-filter-section">
                    <div class="row">
                        <div class="col-md-8 mb-3 mb-md-0">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Cari proyek, nama mahasiswa, atau deskripsi...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="categoryFilter" class="form-control filter-dropdown">
                                <option value="all">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['nama_kategori']) ?>">
                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    <div id="activeFilters" class="mt-2" style="display: none;">
                        <span class="badge bg-primary me-2 mb-2" id="searchBadge"></span>
                        <span class="badge bg-secondary me-2 mb-2" id="categoryBadge"></span>
                    </div>
                </div>

                <!-- NEW: Projects Container for AJAX Loading -->
                <div id="exploreProjectsContainer" class="row">
                    <!-- Projects will be loaded here via AJAX -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat proyek...</p>
                    </div>
                </div>

                <!-- NEW: No Results Message -->
                <div id="noResults" class="no-results" style="display: none;">
                    <i class="bi bi-search"></i>
                    <h3>Tidak ada proyek yang ditemukan</h3>
                    <p class="text-muted">Coba kata kunci atau kategori lain.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 Politeknik Negeri Batam - Projek PBL IFPagi 1A-5</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- NEW: JavaScript for Search & Filter -->
    <script>
        // Load projects when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadProjects();
        });

        // Search input debounce
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadProjects();
            }, 500); // Wait 500ms after typing stops
        });

        // Category filter change
        document.getElementById('categoryFilter').addEventListener('change', function () {
            loadProjects();
        });

        // Load projects from API
        function loadProjects() {
            const search = document.getElementById('searchInput').value.trim();
            const kategori = document.getElementById('categoryFilter').value;
            const container = document.getElementById('exploreProjectsContainer');
            const noResults = document.getElementById('noResults');

            // Update active filters display
            updateActiveFilters(search, kategori);

            // Build API URL
            const apiUrl = 'api/search_projects.php?' +
                (search ? 'search=' + encodeURIComponent(search) + '&' : '') +
                (kategori ? 'kategori=' + encodeURIComponent(kategori) : '');

            // Show loading
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat proyek...</p>
                </div>
            `;
            noResults.style.display = 'none';

            // Fetch from API
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        renderProjects(data.data);
                    } else {
                        container.innerHTML = '';
                        noResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="error-message">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Terjadi kesalahan saat memuat proyek. Silakan coba lagi.
                            </div>
                        </div>
                    `;
                });
        }

        // Render projects to DOM
        function renderProjects(projects) {
            const container = document.getElementById('exploreProjectsContainer');
            container.innerHTML = '';

            projects.forEach(project => {
                const projectCard = `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card project-card">
                            ${project.gambar
                        ? `<img src="../uploads/${project.gambar}?t=${new Date().getTime()}" class="card-img-top" alt="Project Image">`
                        : `<img src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect fill=%22%23eeeeee%22 width=%22400%22 height=%22200%22/%3E%3Ctext fill=%22%23999999%22 font-family=%22Arial%22 font-size=%2218%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ETidak Ada Gambar%3C/text%3E%3C/svg%3E" class="card-img-top" alt="Project Image">`
                    }
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">${escapeHtml(project.judul)}</h5>
                                <p class="card-text"><small class="text-muted">Kategori: ${escapeHtml(project.nama_kategori || 'Tidak ada kategori')}</small></p>
                                <p class="card-text">${escapeHtml(project.deskripsi || '').substring(0, 80)}...</p>

                                <!-- Mahasiswa Info -->
                                <div class="mahasiswa-info">
                                    ${project.mahasiswa_foto
                        ? `<img src="../uploads/${project.mahasiswa_foto}?t=${new Date().getTime()}" class="mahasiswa-avatar" alt="Avatar">`
                        : `<div class="mahasiswa-avatar bg-primary text-white d-flex align-items-center justify-content-center">${getInitials(project.nama_lengkap)}</div>`
                    }
                                    <div>
                                        <p class="mahasiswa-name">${escapeHtml(project.nama_lengkap)}</p>
                                        <p class="mahasiswa-details">${escapeHtml(project.nim || '')} • ${escapeHtml(project.jurusan || '')}</p>
                                    </div>
                                </div>

                                <!-- Demo Button -->
                                ${project.link_demo
                        ? `<a href="${escapeHtml(project.link_demo)}" target="_blank" class="btn btn-sm btn-danger mb-2 mt-2">
                                        <i class="bi bi-youtube"></i> Lihat Video
                                       </a>`
                        : ''
                    }
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += projectCard;
            });
        }

        // Update active filters display
        function updateActiveFilters(search, kategori) {
            const container = document.getElementById('activeFilters');
            const searchBadge = document.getElementById('searchBadge');
            const categoryBadge = document.getElementById('categoryBadge');

            if (!search && (kategori === 'all' || !kategori)) {
                container.style.display = 'none';
                return;
            }

            container.style.display = 'block';

            if (search) {
                searchBadge.textContent = 'Search: ' + escapeHtml(search);
                searchBadge.style.display = 'inline-block';
            } else {
                searchBadge.style.display = 'none';
            }

            if (kategori && kategori !== 'all') {
                categoryBadge.textContent = 'Kategori: ' + escapeHtml(kategori);
                categoryBadge.style.display = 'inline-block';
            } else {
                categoryBadge.style.display = 'none';
            }
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Get initials for avatar placeholder
        function getInitials(name) {
            return name.split(' ')
                .map(n => n[0])
                .join('')
                .toUpperCase()
                .substring(0, 2);
        }
    </script>
</body>

</html>