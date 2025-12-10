<?php
// --- 1. CEK KEAMANAN ---
// Pastikan hanya user yang sudah login dan berperan sebagai 'mahasiswa' yang bisa akses halaman ini.
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// --- 2. AMBIL DATA MAHASISWA DAN PROYEK ---
try {
    // Ambil data mahasiswa untuk navbar
    $sql_mahasiswa = "SELECT m.id, m.nama_lengkap, m.foto_profil, m.nim, m.jurusan
                      FROM mahasiswa m
                      WHERE m.id = (SELECT id_mahasiswa FROM users WHERE id = ?)";
    $stmt_mahasiswa = $pdo->prepare($sql_mahasiswa);
    $stmt_mahasiswa->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);

    if (!$mahasiswa) {
        die("Data mahasiswa tidak ditemukan.");
    }

    // Ambil data proyek MILIK MAHASISWA INI SAJA, beserta data mahasiswa untuk ditampilkan di kartu
    $sql_projects = "SELECT p.id, p.judul, p.deskripsi, p.kategori, p.gambar, p.link_demo, p.tanggal, m.nama_lengkap, m.nim, m.jurusan
                    FROM projects p
                    JOIN mahasiswa m ON p.id_mahasiswa = m.id
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
    <title>WorkPiece - Proyek Saya</title>

    <!-- Google Font Poppins (SAMA DENGAN LANDING PAGE) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <style>
        /* --- GAYA UMUM (SAMA DENGAN LANDING PAGE) --- */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif; /* FONT SUDAH DISAMAKAN */
            color: #333; /* Teks gelap untuk kontras */
            background-color: whitesmoke; /* BACKGROUND SAMA */
            padding-top: 76px; /* Ruang untuk navbar */
        }

        /* --- NAVBAR (SAMA DENGAN LANDING PAGE) --- */
        .navbar {
            background: rgba(0, 0, 60, 0.8) !important;
            padding: 0.75rem 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff !important;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            margin-left: 25px;
            transition: color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #00ffff !important;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 8px;
        }

        /* --- GAYA KARTU PROYEK (DIPERINDAH) --- */
        .project-container {
            padding: 60px 0;
        }

        .project-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            overflow: hidden; /* Agar gambar tidak keluar dari sudut */
        }

        .project-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .project-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .project-card .card-body {
            padding: 25px;
            display: flex;
            flex-direction: column;
        }

        .project-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #003366;
            margin-bottom: 10px;
        }

        .project-card .card-text {
            color: #666;
            font-size: 0.95rem;
            flex-grow: 1; /* Mendorong tombol ke bawah */
        }

        /* --- GAYA INFO MAHASISWA DI DALAM KARTU --- */
        .student-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #555;
        }

        .student-info p {
            margin: 0;
        }

        .student-info i {
            color: #55bddd;
            margin-right: 8px;
        }

        /* --- GAYA TOMBOL --- */
        .btn-custom {
            border-radius: 20px;
            font-weight: 500;
            padding: 8px 15px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <!-- Navbar (SAMA DENGAN SEBELUMNYA) -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand ms-3" href="home_mhs.php">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="home_mhs.php">Proyek Saya</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php if (!empty($mahasiswa['foto_profil'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($mahasiswa['foto_profil']) ?>" class="profile-img" alt="Profile">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 me-1"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>
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

    <!-- Main Content: Daftar Proyek (TANPA DASHBOARD/HERO) -->
    <main class="project-container">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Proyek Saya</h2>
                <p class="text-muted">Kelola dan tampilkan karya terbaikmu di sini.</p>
                <a href="upload_project.php" class="btn btn-primary btn-lg mt-2">
                    <i class="bi bi-plus-circle"></i> Tambah Proyek Baru
                </a>
            </div>

            <div class="row g-4">
                <?php if (empty($projects)): ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. <a href="upload_project.php">Tambahkan proyek pertama Anda!</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="project-card">
                                <?php if (!empty($project['gambar'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($project['gambar']) ?>" class="card-img-top" alt="Project Image">
                                <?php else: ?>
                                    <img src="https://picsum.photos/seed/project<?= $project['id'] ?>/400/200.jpg" class="card-img-top" alt="Project Image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($project['judul']); ?></h5>
                                    <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 100) . '...'; ?></p>

                                    <!-- TOMBOL VIDEO (JIKA ADA) -->
                                    <?php if (!empty($project['link_demo'])): ?>
                                        <a href="<?= htmlspecialchars($project['link_demo']) ?>" target="_blank" class="btn btn-sm btn-danger mb-3">
                                            <i class="bi bi-youtube"></i> Lihat Video
                                        </a>
                                    <?php endif; ?>

                                    <!-- INFO MAHASISWA (TAMBAHAN) -->
                                    <div class="student-info">
                                        <p><i class="bi bi-person-fill"></i> <strong><?= htmlspecialchars($project['nama_lengkap']) ?></strong></p>
                                        <p><i class="bi bi-card-text"></i> <?= htmlspecialchars($project['nim']) ?></p>
                                        <p><i class="bi bi-building"></i> <?= htmlspecialchars($project['jurusan']) ?></p>
                                    </div>

                                    <div class="d-flex justify-content-between mt-auto">
                                        <a href="edit_project.php?id=<?= $project['id']; ?>" class="btn btn-outline-primary btn-custom">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="proses_hapus_project.php?id=<?= $project['id']; ?>" class="btn btn-outline-danger btn-custom" onclick="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>