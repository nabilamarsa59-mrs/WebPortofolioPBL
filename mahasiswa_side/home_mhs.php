<?php
// --- 1. CEK KEAMANAN ---
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// --- 2. AMBIL DATA MAHASISWA & PROYEK (CARA LEBIH SEDERHANA DAN PASTI) ---
try {
    // Satu query langsung untuk ambil data mahasiswa berdasarkan session user_id
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

    // Query untuk mengambil semua project milik mahasiswa ini berdasarkan ID mahasiswa yang sudah didapat
    $sql_projects = "SELECT p.id, p.judul, p.deskripsi, p.kategori, p.gambar, p.link_demo, p.tanggal
                    FROM projects p
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #003366; --secondary-color: #001F3F; --accent-color: #55bddd; }
        body { font-family: 'Poppins', sans-serif; background-color: whitesmoke; color: #333; padding-top: 76px; }
        .navbar { background-color: var(--primary-color); box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
        .navbar-brand, .navbar-nav .nav-link, .dropdown-item { color: #fff !important; }
        .navbar-nav .nav-link:hover, .dropdown-item:hover { color: var(--accent-color) !important; }
        .dropdown-menu { background-color: var(--secondary-color); border: none; }
        .hero { background: linear-gradient(rgba(0, 51, 102, 0.7), rgba(0, 31, 63, 0.7)), url('../bg-gedung.jpg') no-repeat center center/cover; color: #fff; padding: 100px 0; text-align: center; margin-bottom: 30px; }
        .project-card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; border: none; box-shadow: 0 4px 8px rgba(0,0,0,0.05); height: 100%; }
        .project-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .card-img-top { height: 200px; object-fit: cover; }
        .profile-img { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 8px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home_mhs.php">WorkPiece</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="home_mhs.php">Dashboard</a></li>
                </ul>
                <ul class="navbar-nav">
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Selamat datang, <span><?= htmlspecialchars($mahasiswa['nama_lengkap']); ?>!</span></h1>
            <p class="lead">Kelola dan tampilkan karya terbaikmu di sini.</p>
            <a href="upload_project.php" class="btn btn-lg btn-light mt-3"><i class="bi bi-plus-circle"></i> Tambah Proyek Baru</a>
        </div>
    </section>

    <!-- Main Content: Daftar Proyek -->
    <section class="container my-5">
        <h2>Proyek Saya</h2>
        <div class="row">
            <?php if (empty($projects)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle"></i> Anda belum memiliki proyek. <a href="upload_project.php">Tambahkan proyek pertama Anda!</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card project-card">
                            <?php if (!empty($project['gambar'])): ?>
                                <img src="/WebPortofolioPBL/uploads/<?= htmlspecialchars($project['gambar'] ?? 'default-project.png') ?>" class="card-img-top" alt="Project Image">
                            <?php else: ?>
                                <img src="https://picsum.photos/seed/project<?= $project['id'] ?>/400/200.jpg" class="card-img-top" alt="Project Image">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($project['judul']); ?></h5>
                                <p class="card-text"><small class="text-muted">Kategori: <?= htmlspecialchars($project['kategori']); ?></small></p>
                                <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>

                                <!-- Tampilkan tombol video hanya jika ada linknya -->
                                <?php if (!empty($project['link_demo'])): ?>
                                    <a href="<?= htmlspecialchars($project['link_demo']) ?>" target="_blank" class="btn btn-sm btn-danger mb-2">
                                        <i class="bi bi-youtube"></i> Lihat Video
                                    </a>
                                <?php endif; ?>

                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="edit_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="proses_hapus_project.php?id=<?= $project['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus proyek ini?');">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>