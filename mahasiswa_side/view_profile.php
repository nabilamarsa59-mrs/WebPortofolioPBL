<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$id_mahasiswa_view = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id_mahasiswa_view) {
    header("Location: home_mhs.php");
    exit();
}

try {
    $sql_mahasiswa = "SELECT m.id, m.nama_lengkap, m.foto_profil, m.nim, m.jurusan, m.email
                      FROM mahasiswa m
                      WHERE m.id = ?";
    $stmt_mahasiswa = $pdo->prepare($sql_mahasiswa);
    $stmt_mahasiswa->execute([$id_mahasiswa_view]);
    $mahasiswa_view = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);

    if (!$mahasiswa_view) {
        header("Location: home_mhs.php");
        exit();
    }

    $sql_projects = "SELECT p.id, p.judul, p.deskripsi, k.nama_kategori, p.gambar, p.link_demo, p.tanggal
                     FROM projects p
                     LEFT JOIN kategori_proyek k ON p.kategori = k.id
                     WHERE p.id_mahasiswa = ?
                     ORDER BY p.tanggal DESC";
    $stmt_projects = $pdo->prepare($sql_projects);
    $stmt_projects->execute([$id_mahasiswa_view]);
    $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?= htmlspecialchars($mahasiswa_view['nama_lengkap']) ?> - WorkPiece</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            padding-top: 76px;
            margin: 0;
        }

        .navbar {
            background: #00003c !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 0;
            min-height: 76px;
            margin: 0 !important;
            width: 100%;
        }

        .navbar .container {
            margin: 0 auto;
            padding-left: 1rem;
            padding-right: 1rem;
            max-width: 100%;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #fff !important;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            margin-left: 20px;
            transition: color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #55bddd !important;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .profile-header {
            background: linear-gradient(135deg, #003366 0%, #001F3F 100%);
            color: white;
            padding: 3rem 0;
            margin: 0 !important;
            width: 100%;
        }

        .profile-header .container {
            padding-left: 1rem;
            padding-right: 1rem;
            max-width: 100%;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            background-color: #003366;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            margin: 0 auto;
        }

        .profile-info {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item i {
            font-size: 1.2rem;
            color: #003366;
            width: 30px;
            flex-shrink: 0;
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

        .project-description {
            position: relative;
            line-height: 1.6;
        }
        
        .description-short {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .description-full {
            display: none;
            white-space: pre-line;
        }
        
        .description-full.show {
            display: block;
        }
        
        .read-more-btn {
            color: #003366;
            background: none;
            border: none;
            padding: 5px 0;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            margin-top: 5px;
            display: inline-block;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .read-more-btn:hover {
            color: #001a4d;
            text-decoration: underline;
        }

        .main-content-section {
            padding: 2rem 0;
            margin: 0;
        }

        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin: 0;
        }

        .back-btn {
            background-color: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .back-btn:hover {
            background-color: white;
            color: #003366;
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .navbar-nav .nav-link {
                margin-left: 0;
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 767.98px) {
            body {
                padding-top: 70px;
            }

            .navbar {
                min-height: auto;
                padding: 0.5rem 0;
            }

            .navbar .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .navbar-brand {
                font-size: 1.25rem;
            }

            .profile-header {
                padding: 2rem 0;
            }

            .profile-header .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .profile-avatar {
                width: 120px;
                height: 120px;
                font-size: 2.5rem;
            }

            .profile-header h2 {
                font-size: 1.5rem;
            }

            .profile-header p {
                font-size: 0.9rem;
            }

            .back-btn {
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }

            .card-img-top {
                height: 180px;
            }

            .info-item {
                font-size: 0.9rem;
            }

            .main-content-section {
                padding: 1.5rem 0;
            }
        }

        @media (max-width: 575.98px) {
            body {
                padding-top: 70px;
            }

            .navbar {
                padding: 0.5rem 0 !important;
                min-height: 70px;
            }

            .navbar .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .profile-header {
                padding: 1.5rem 0;
            }

            .profile-header .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .main-content-section .container {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 2rem;
                border: 3px solid white;
            }

            .profile-header {
                padding: 1.5rem 0;
            }

            .profile-header h2 {
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
            }

            .profile-header p {
                font-size: 0.85rem;
            }

            .back-btn {
                padding: 0.375rem 0.875rem;
                font-size: 0.85rem;
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

            h4 {
                font-size: 1.1rem;
            }

            h5 {
                font-size: 1rem;
            }

            .info-item {
                padding: 0.5rem 0;
            }

            .info-item i {
                font-size: 1rem;
                width: 25px;
            }

            .read-more-btn {
                font-size: 0.85rem;
            }

            .main-content-section {
                padding: 1rem 0;
            }

            .profile-info {
                padding: 1rem;
                margin-bottom: 1.5rem;
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
                    <li class="nav-item">
                        <a class="nav-link" href="home_mhs.php">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <section class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
                    <?php if (!empty($mahasiswa_view['foto_profil'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($mahasiswa_view['foto_profil']) ?>?t=<?= time() ?>" 
                             class="profile-avatar" alt="Profile Picture"
                             onerror="this.outerHTML='<div class=\'profile-avatar\'><?= substr($mahasiswa_view['nama_lengkap'], 0, 2) ?></div>';">
                    <?php else: ?>
                        <div class="profile-avatar">
                            <?= strtoupper(substr($mahasiswa_view['nama_lengkap'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-9 text-center text-md-start">
                    <h2 class="mb-2"><?= htmlspecialchars($mahasiswa_view['nama_lengkap']) ?></h2>
                    <p class="mb-3"><i class="bi bi-person-badge me-2"></i><?= htmlspecialchars($mahasiswa_view['nim']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="main-content-section">
        <div class="container">
            <div class="row">
                <!-- Profile Info Sidebar -->
                <div class="col-12 col-lg-4 mb-4">
                    <div class="profile-info">
                        <h5 class="mb-3" style="color: #003366; font-weight: 600;">Informasi Mahasiswa</h5>
                        
                        <div class="info-item">
                            <i class="bi bi-building"></i>
                            <div>
                                <small class="text-muted d-block">Jurusan</small>
                                <strong><?= htmlspecialchars($mahasiswa_view['jurusan']) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bi bi-person-badge"></i>
                            <div>
                                <small class="text-muted d-block">NIM</small>
                                <strong><?= htmlspecialchars($mahasiswa_view['nim']) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <i class="bi bi-folder"></i>
                            <div>
                                <small class="text-muted d-block">Total Proyek</small>
                                <strong><?= count($projects) ?> Proyek</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Projects Section -->
                <div class="col-12 col-lg-8">
                    <h4 class="mb-4" style="color: #003366;">
                        <i class="bi bi-folder-fill me-2"></i>Proyek dari <?= htmlspecialchars($mahasiswa_view['nama_lengkap']) ?>
                    </h4>

                    <?php if (empty($projects)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Mahasiswa ini belum memiliki proyek yang dipublikasikan.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($projects as $project): ?>
                                <div class="col-12 col-sm-6 mb-4">
                                    <div class="card project-card">
                                        <?php
                                        $project_image = '../uploads/default-project.png';
                                        if (!empty($project['gambar'])) {
                                            $project_image = '../uploads/' . $project['gambar'];
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($project_image) ?>?t=<?= time() ?>" 
                                             class="card-img-top" alt="Project Image"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22200%22%3E%3Crect fill=%22%23eee%22 width=%22400%22 height=%22200%22/%3E%3Ctext fill=%22%23999%22 font-family=%22Arial%22 font-size=%2218%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3ETidak Ada Gambar%3C/text%3E%3C/svg%3E'">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($project['judul']); ?></h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Kategori: <?= htmlspecialchars($project['nama_kategori'] ?? 'Tidak ada kategori'); ?>
                                                </small>
                                            </p>
                                            
                                            <!-- Deskripsi dengan Read More -->
                                            <div class="project-description">
                                                <div class="description-short" id="desc-short-<?= $project['id'] ?>">
                                                    <p class="card-text"><?= nl2br(htmlspecialchars($project['deskripsi'])) ?></p>
                                                </div>
                                                <div class="description-full" id="desc-full-<?= $project['id'] ?>">
                                                    <p class="card-text"><?= nl2br(htmlspecialchars($project['deskripsi'])) ?></p>
                                                </div>
                                                
                                                <?php if (strlen($project['deskripsi']) > 150): ?>
                                                    <button class="read-more-btn" 
                                                            onclick="toggleDescription(<?= $project['id'] ?>)"
                                                            id="btn-<?= $project['id'] ?>">
                                                        Lihat lebih lengkap <i class="bi bi-chevron-down"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if (!empty($project['link_demo'])): ?>
                                                <a href="<?= htmlspecialchars($project['link_demo']) ?>" 
                                                   target="_blank" class="btn btn-sm btn-danger mt-2">
                                                    <i class="bi bi-youtube"></i> Lihat Video
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
        function toggleDescription(projectId) {
            const shortDesc = document.getElementById('desc-short-' + projectId);
            const fullDesc = document.getElementById('desc-full-' + projectId);
            const btn = document.getElementById('btn-' + projectId);
            const icon = btn.querySelector('i');
            
            if (fullDesc.classList.contains('show')) {
                fullDesc.classList.remove('show');
                shortDesc.style.display = '-webkit-box';
                btn.innerHTML = 'Lihat lebih lengkap <i class="bi bi-chevron-down"></i>';
            } else {
                shortDesc.style.display = 'none';
                fullDesc.classList.add('show');
                btn.innerHTML = 'Lihat lebih sedikit <i class="bi bi-chevron-up"></i>';
            }
        }
    </script>
</body>
</html>