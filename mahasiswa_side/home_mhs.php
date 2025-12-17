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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home_mhs.php">WorkPiece</a>
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

    <!-- Main Content: Daftar Proyek -->
    <section class="container my-5">
        <h2>Proyek Saya</h2>
        <div class="row">
            <?php if (empty($projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. <a href="upload_project.php">Tambahkan
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
                                        <?= htmlspecialchars($project['nama_kategori'] ?? 'Tidak ada kategori'); ?></small></p>
                                <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>

                                <!-- Tampilkan tombol video hanya jika ada linknya -->
                                <?php if (!empty($project['link_demo'])): ?>
                                    <a href="<?= htmlspecialchars($project['link_demo']) ?>" target="_blank"
                                        class="btn btn-sm btn-danger mb-2">
                                        <i class="bi bi-youtube"></i> Lihat Video
                                    </a>
                                <?php endif; ?>

                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="edit_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-primary">
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
    </section>
    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 Politeknik Negeri Batam - Projek PBL IFPagi 1A-5</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>