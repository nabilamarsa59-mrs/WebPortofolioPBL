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

    $sql_projects = "SELECT p.id, p.judul, p.deskripsi, k.nama_kategori, p.gambar, p.link_demo, p.tanggal
                FROM projects p
                LEFT JOIN kategori_proyek k ON p.kategori = k.id
                WHERE p.id_mahasiswa = ?
                ORDER BY p.tanggal DESC";

    $stmt_projects = $pdo->prepare($sql_projects);
    $stmt_projects->execute([$mahasiswa['id']]);
    $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

    $sql_kategori = "SELECT * FROM kategori_proyek ORDER BY nama_kategori ASC";
    $stmt_kategori = $pdo->query($sql_kategori);
    $categories = $stmt_kategori->fetchAll(PDO::FETCH_ASSOC);

    $sql_jurusan = "SELECT DISTINCT jurusan FROM mahasiswa WHERE jurusan IS NOT NULL ORDER BY jurusan ASC";
    $stmt_jurusan = $pdo->query($sql_jurusan);
    $jurusan_list = $stmt_jurusan->fetchAll(PDO::FETCH_ASSOC);

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
            --navbar-height: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            color: #333;
            padding-top: var(--navbar-height);
        }

        .navbar {
            background: #00003c !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            min-height: var(--navbar-height);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #fff !important;
        }

        .navbar-nav {
            align-items: center;
        }

        .navbar-nav .nav-item {
            margin-left: 15px;
        }

        .navbar-brand, .navbar-nav .nav-link, .dropdown-item {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover, .dropdown-item:hover {
            color: var(--accent-color) !important;
        }

        .dropdown-menu {
            background-color: var(--secondary-color);
            border: none;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .hero {
            min-height: 100vh;
            background: linear-gradient(rgba(0, 30, 100, 0.5), rgba(0, 30, 100, 0.5)), url('../bg-gedung.jpg') no-repeat center center/cover;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem 1rem;
        }

        .hero-content h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-content span {
            color: #55bddd;
        }

        .hero-content p {
            font-size: clamp(1rem, 3vw, 1.5rem);
            margin-bottom: 1.5rem;
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
            margin-top: 80px;
        }

        .view-tabs {
            margin: 2rem 0;
            border-bottom: 2px solid #e0e0e0;
            overflow-x: auto;
            white-space: nowrap;
        }

        .view-tabs .nav-link {
            color: #666;
            font-weight: 600;
            padding: 1rem 2rem;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
        }

        .view-tabs .nav-link:hover {
            color: var(--primary-color);
        }

        .view-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--primary-color);
        }

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
            width: 100%;
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
            width: 100%;
        }

        .filter-dropdown:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.1);
            outline: none;
        }

        .mahasiswa-info {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 0.5rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .mahasiswa-info:hover {
            background: #e9ecef;
        }

        .mahasiswa-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .mahasiswa-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--primary-color);
            margin: 0;
        }

        .mahasiswa-details {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
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

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .navbar-nav {
                margin-top: 1rem;
            }

            .navbar-nav .nav-item {
                margin-left: 0;
                margin-bottom: 0.5rem;
            }

            .hero {
                min-height: 80vh;
            }

            .view-tabs .nav-link {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 767.98px) {
            body {
                padding-top: 70px;
            }

            :root {
                --navbar-height: 70px;
            }

            .navbar {
                min-height: 70px;
                padding: 0.5rem 1rem;
            }

            .navbar-brand {
                font-size: 1.25rem;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .search-filter-section {
                padding: 1rem;
            }

            .search-box input {
                font-size: 0.9rem;
                padding: 0.625rem 1rem 0.625rem 40px;
            }

            .filter-dropdown {
                font-size: 0.9rem;
                padding: 0.625rem 0.875rem;
                margin-bottom: 0.75rem;
            }

            .card-img-top {
                height: 180px;
            }

            .mahasiswa-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .mahasiswa-name {
                font-size: 0.85rem;
            }

            .mahasiswa-details {
                font-size: 0.75rem;
            }

            .footer-custom {
                margin-top: 40px;
                padding: 15px 0;
            }

            .hero {
                min-height: calc(100vh - 70px);
                padding: 1.5rem 1rem;
            }

            .profile-img {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding-top: 60px;
            }

            :root {
                --navbar-height: 60px;
            }

            .navbar {
                min-height: 60px;
                padding: 0.5rem 0.75rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .hero {
                min-height: calc(100vh - 60px);
                padding: 1rem 0.75rem;
            }

            .hero-content h1 {
                font-size: 1.5rem;
                margin-bottom: 15px;
            }

            .hero-content p {
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }

            .btn-lg {
                font-size: 0.9rem;
                padding: 0.625rem 1.25rem;
            }

            .card-body {
                padding: 1rem;
            }

            .card-title {
                font-size: 1rem;
            }

            .card-text {
                font-size: 0.875rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.375rem 0.75rem;
            }

            .profile-img {
                width: 32px;
                height: 32px;
                margin-right: 6px;
            }

            .dropdown-toggle {
                font-size: 0.85rem;
            }

            .dropdown-menu {
                font-size: 0.9rem;
            }

            .view-tabs .nav-link {
                padding: 0.625rem 0.75rem;
                font-size: 0.85rem;
            }

            .search-filter-section {
                padding: 0.75rem;
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 400px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .hero-content h1 {
                font-size: 1.3rem;
            }

            .hero-content p {
                font-size: 0.85rem;
            }

            .profile-img {
                width: 28px;
                height: 28px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home_mhs.php">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!empty($mahasiswa['foto_profil'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($mahasiswa['foto_profil']) ?>?t=<?= time() ?>"
                                    class="profile-img" alt="Profile">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 me-1"></i>
                            <?php endif; ?>
                            <span class="d-none d-md-inline"><?= htmlspecialchars($mahasiswa['nama_lengkap']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
    <section id="beranda" class="hero">
        <div class="hero-content">
            <h1>Selamat datang, <span><?= htmlspecialchars($mahasiswa['nama_lengkap']); ?>!</span></h1>
            <p>Kelola dan tampilkan karya terbaikmu di sini.</p>
            <a href="upload_project.php" class="btn btn-lg btn-light mt-3">
                <i class="bi bi-plus-circle"></i> Tambah Proyek Baru
            </a>
        </div>
    </section>
    <!-- Main Content -->
    <section class="container my-5">
        <!-- Tab Navigation -->
        <ul class="nav nav-pills view-tabs" id="viewTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-projects" data-bs-toggle="pill"
                    data-bs-target="#projects-section" type="button" role="tab">
                    <i class="bi bi-folder me-2"></i>Proyek Saya
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-explore" data-bs-toggle="pill" data-bs-target="#explore-section"
                    type="button" role="tab">
                    <i class="bi bi-globe me-2"></i>Jelajah Proyek
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="viewTabContent">
            <!-- Proyek Saya Tab -->
            <div class="tab-pane fade show active" id="projects-section" role="tabpanel">
                <h2 class="mb-4">Proyek Saya</h2>
                <div class="row">
                    <?php if (empty($projects)): ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center" role="alert">
                                <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. 
                                <a href="upload_project.php">Tambahkan proyek pertama Anda!</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <div class="col-12 col-sm-6 col-lg-4 mb-4">
                                <div class="card project-card">
                                    <?php
                                    $project_image = '../uploads/default-project.png';
                                    if (!empty($project['gambar'])) {
                                        $project_image = '../uploads/' . $project['gambar'];
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($project_image) ?>?t=<?= time() ?>" class="card-img-top" alt="Project Image">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($project['judul']); ?></h5>
                                        <p class="card-text"><small class="text-muted">Kategori: <?= htmlspecialchars($project['nama_kategori'] ?? 'Tidak ada kategori'); ?></small></p>
                                        <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>

                                        <?php if (!empty($project['link_demo'])): ?>
                                            <a href="<?= htmlspecialchars($project['link_demo']) ?>" target="_blank" class="btn btn-sm btn-danger mb-2">
                                                <i class="bi bi-youtube"></i> Lihat Video
                                            </a>
                                        <?php endif; ?>

                                        <div class="mt-auto d-flex flex-column flex-sm-row justify-content-between gap-2">
                                            <a href="edit_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="proses_hapus_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-danger"
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

            <!-- Jelajah Proyek Mahasiswa Lain Tab -->
            <div class="tab-pane fade" id="explore-section" role="tabpanel">
                <h2 class="mb-4">Jelajah Proyek Mahasiswa Lain</h2>

                <!-- Search & Filter Section -->
                <div class="search-filter-section">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchInput" class="form-control" placeholder="Cari proyek, nama mahasiswa, atau deskripsi...">
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <select id="categoryFilter" class="form-control filter-dropdown">
                                <option value="all">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['nama_kategori']) ?>">
                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <select id="jurusanFilter" class="form-control filter-dropdown">
                                <option value="all">Semua Jurusan</option>
                                <?php foreach ($jurusan_list as $jurusan): ?>
                                    <option value="<?= htmlspecialchars($jurusan['jurusan']) ?>">
                                        <?= htmlspecialchars($jurusan['jurusan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Projects Container -->
                <div id="exploreProjectsContainer" class="row">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat proyek...</p>
                    </div>
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="no-results" style="display: none;">
                    <i class="bi bi-search"></i>
                    <h3>Tidak ada proyek yang ditemukan</h3>
                    <p class="text-muted">Coba kata kunci, kategori, atau jurusan lain.</p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const currentUserId = <?= $mahasiswa['id'] ?>;

        document.getElementById('tab-explore').addEventListener('click', function() {
            loadProjects();
        });

        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadProjects(), 500);
        });

        document.getElementById('categoryFilter').addEventListener('change', function () {
            loadProjects();
        });

        document.getElementById('jurusanFilter').addEventListener('change', function () {
            loadProjects();
        });

        function loadProjects() {
            const search = document.getElementById('searchInput').value.trim();
            const kategori = document.getElementById('categoryFilter').value;
            const jurusan = document.getElementById('jurusanFilter').value;
            const container = document.getElementById('exploreProjectsContainer');
            const noResults = document.getElementById('noResults');

            const apiUrl = '../api/search_projects.php?' +
                (search ? 'search=' + encodeURIComponent(search) + '&' : '') +
                (kategori && kategori !== 'all' ? 'kategori=' + encodeURIComponent(kategori) + '&' : '') +
                (jurusan && jurusan !== 'all' ? 'jurusan=' + encodeURIComponent(jurusan) : '');

            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat proyek...</p>
                </div>
            `;
            noResults.style.display = 'none';

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        const otherProjects = data.data.filter(project => project.id_mahasiswa != currentUserId);
                        
                        if (otherProjects.length > 0) {
                            renderProjects(otherProjects);
                        } else {
                            container.innerHTML = '';
                            noResults.style.display = 'block';
                        }
                    } else {
                        container.innerHTML = '';
                        noResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Terjadi kesalahan saat memuat proyek. Silakan coba lagi.
                            </div>
                        </div>
                    `;
                });
        }

        function renderProjects(projects) {
            const container = document.getElementById('exploreProjectsContainer');
            container.innerHTML = '';

            projects.forEach(project => {
                const projectCard = document.createElement('div');
                projectCard.className = 'col-12 col-sm-6 col-lg-4 mb-4';
                
                projectCard.innerHTML = `
                    <div class="card project-card">
                        <img src="../uploads/${project.gambar || 'default-project.png'}?t=${Date.now()}" 
                             class="card-img-top" alt="Project Image"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect fill=%22%23eee%22 width=%22400%22 height=%22200%22/%3E%3Ctext fill=%22%23999%22 font-family=%22Arial%22 font-size=%2218%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3ETidak Ada Gambar%3C/text%3E%3C/svg%3E'">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${escapeHtml(project.judul)}</h5>
                            <p class="card-text"><small class="text-muted">Kategori: ${escapeHtml(project.nama_kategori || 'Tidak ada kategori')}</small></p>
                            <p class="card-text">${escapeHtml(project.deskripsi || '').substring(0, 80)}...</p>

                            <div class="mahasiswa-info" onclick="window.location.href='view_profile.php?id=${project.id_mahasiswa}'">
                                ${project.mahasiswa_foto 
                                    ? `<img src="../uploads/${project.mahasiswa_foto}?t=${Date.now()}" class="mahasiswa-avatar" alt="Avatar" onerror="this.outerHTML='<div class=\\'mahasiswa-avatar\\'>${getInitials(project.nama_lengkap)}</div>';">`
                                    : `<div class="mahasiswa-avatar">${getInitials(project.nama_lengkap)}</div>`
                                }
                                <div class="flex-grow-1">
                                    <p class="mahasiswa-name">${escapeHtml(project.nama_lengkap)}</p>
                                    <p class="mahasiswa-details">${escapeHtml(project.nim || '')} • ${escapeHtml(project.jurusan || '')}</p>
                                </div>
                            </div>

                            ${project.link_demo 
                                ? `<a href="${escapeHtml(project.link_demo)}" target="_blank" class="btn btn-sm btn-danger mt-2">
                                       <i class="bi bi-youtube"></i> Lihat Video
                                   </a>`
                                : ''
                            }
                        </div>
                    </div>
                `;
                
                container.appendChild(projectCard);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function getInitials(name) {
            return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        }
    </script>
</body>
</html>