<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil data mahasiswa untuk navbar
try {
    $sql_mahasiswa_nav = "SELECT m.id, m.nama_lengkap, m.foto_profil
                          FROM users u
                          JOIN mahasiswa m ON u.id_mahasiswa = m.id
                          WHERE u.id = ?";
    $stmt_nav = $pdo->prepare($sql_mahasiswa_nav);
    $stmt_nav->execute([$_SESSION['user_id']]);
    $mahasiswa_nav = $stmt_nav->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error saat mengambil data: " . $e->getMessage());
}

 $id_project = $_GET['id'] ?? null;

if (!$id_project) {
    header("Location: home_mhs.php");
    exit();
}

 $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
 $stmt->execute([$id_project]);
 $project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: home_mhs.php");
    exit();
}

 $stmt_mahasiswa = $pdo->prepare("SELECT m.id, m.jurusan FROM users u JOIN mahasiswa m ON u.id_mahasiswa = m.id WHERE u.id = ?");
 $stmt_mahasiswa->execute([$_SESSION['user_id']]);
 $mahasiswa_data = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa_data) {
    header("Location: home_mhs.php");
    exit();
}

 $id_mahasiswa = $mahasiswa_data['id'];
 $jurusan_mahasiswa = $mahasiswa_data['jurusan'];

if ($project['id_mahasiswa'] != $id_mahasiswa) {
    header("Location: home_mhs.php");
    exit();
}

// Ambil kategori sesuai jurusan mahasiswa
 $stmt_kategori = $pdo->prepare("SELECT id, nama_kategori FROM kategori_proyek WHERE jurusan = ? ORDER BY nama_kategori");
 $stmt_kategori->execute([$jurusan_mahasiswa]);
 $kategori_list = $stmt_kategori->fetchAll(PDO::FETCH_ASSOC);

// Cek apakah proyek sudah dinilai (hanya untuk menampilkan informasi, bukan untuk membatasi edit)
 $stmt_penilaian = $pdo->prepare("SELECT * FROM penilaian WHERE id_project = ?");
 $stmt_penilaian->execute([$id_project]);
 $penilaian = $stmt_penilaian->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $link_video = $_POST['link_video'];

    $nama_file_gambar = $project['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid('project_', true) . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            if (!empty($project['gambar']) && file_exists('../uploads/' . $project['gambar'])) {
                unlink('../uploads/' . $project['gambar']);
            }

            $nama_file_gambar = $new_file_name;
        }
    }

    $sql = "UPDATE projects SET judul = ?, deskripsi = ?, kategori = ?, link_demo = ?, gambar = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$judul, $deskripsi, $kategori, $link_video, $nama_file_gambar, $id_project]);

    header("Location: home_mhs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Proyek - WorkPiece</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #00003c !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            z-index: 1000;
            min-height: 80px;
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

        .navbar-brand,
        .navbar-nav .nav-link,
        .dropdown-item {
            color: #fff !important;
        }

        .navbar-nav .nav-link:hover,
        .dropdown-item:hover {
            color: #55bddd !important;
        }

        .dropdown-menu {
            background-color: #001F3F;
            border: none;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .profile-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 8px;
        }

        .main-content {
            padding: 2rem 0;
            flex: 1;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .preview-image {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            border-radius: 10px;
            margin-top: 10px;
            object-fit: cover;
        }

        .btn-primary {
            background-color: #00003c;
            border-color: #00003c;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #001a4d;
            border-color: #001a4d;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #00003c;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
        }

        .grade-info {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
        }

        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin-top: auto;
            width: 100%;
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
        }

        @media (max-width: 767.98px) {
            body {
                padding-top: 70px;
            }

            .navbar {
                min-height: auto;
                padding: 0.5rem 1rem;
            }

            .navbar-brand {
                font-size: 1.25rem;
            }

            .main-content {
                padding: 1rem 0;
            }

            .card {
                margin: 0 0.5rem;
            }

            h2 {
                font-size: 1.5rem;
            }

            .btn-primary {
                width: 100%;
                padding: 0.875rem;
            }

            .preview-image {
                max-height: 200px;
            }

            .profile-img {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 575.98px) {
            .card {
                border-radius: 10px;
            }

            .card-body {
                padding: 1rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control, .form-select {
                font-size: 0.9rem;
            }

            .dropdown-toggle {
                font-size: 0.9rem;
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
                            <?php if (!empty($mahasiswa_nav['foto_profil'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($mahasiswa_nav['foto_profil']) ?>?t=<?= time() ?>"
                                    class="profile-img" alt="Profile">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 me-1"></i>
                            <?php endif; ?>
                            <span class="d-none d-md-inline"><?= htmlspecialchars($mahasiswa_nav['nama_lengkap']) ?></span>
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

    <main class="container main-content">
        <div class="card shadow-lg p-3 p-md-4">
            <h2 class="text-primary mb-2">Edit Proyek PBL</h2>
            <p class="text-muted mb-3">Perbarui informasi proyek Anda</p>

            <div class="info-box">
                <strong>Info Jurusan:</strong> <?= htmlspecialchars($jurusan_mahasiswa) ?><br>
                <small class="text-muted">Kategori proyek yang tersedia disesuaikan dengan jurusan Anda</small>
            </div>

            <?php if ($penilaian): ?>
                <div class="grade-info">
                    <h5 class="text-success"><i class="bi bi-check-circle me-2"></i>Informasi Penilaian</h5>
                    <div class="mt-2">
                        <strong>Nilai:</strong> <?= htmlspecialchars($penilaian['nilai']) ?><br>
                        <strong>Komentar Dosen:</strong> <?= nl2br(htmlspecialchars($penilaian['komentar'])) ?><br>
                        <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= date('d M Y H:i', strtotime($penilaian['tanggal_dinilai'])) ?></small>
                    </div>
                    <div class="mt-2">
                        <small class="text-info"><i class="bi bi-info-circle me-1"></i>Anda masih dapat mengedit proyek ini, tetapi tidak dapat menghapusnya.</small>
                    </div>
                </div>
            <?php endif; ?>

            <form action="edit_project.php?id=<?= $project['id'] ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="judul" class="form-label fw-semibold">Judul Proyek</label>
                    <input type="text" name="judul" class="form-control"
                        value="<?= htmlspecialchars($project['judul']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi Proyek</label>
                    <textarea name="deskripsi" rows="4" class="form-control"
                        required><?= htmlspecialchars($project['deskripsi']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label fw-semibold">Kategori Proyek</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php if (empty($kategori_list)): ?>
                            <option value="" disabled>Tidak ada kategori untuk jurusan Anda</option>
                        <?php else: ?>
                            <?php foreach ($kategori_list as $kat): ?>
                                <option value="<?= $kat['id'] ?>" <?= ($project['kategori'] == $kat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted">Hanya kategori dari jurusan <?= htmlspecialchars($jurusan_mahasiswa) ?> yang ditampilkan</small>
                </div>
                <div class="mb-3">
                    <label for="link_video" class="form-label fw-semibold">Link Video YouTube</label>
                    <input type="url" name="link_video" class="form-control"
                        value="<?= htmlspecialchars($project['link_demo']) ?>">
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label fw-semibold">Foto Proyek</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                    <?php if (!empty($project['gambar'])): ?>
                        <div class="mt-3">
                            <p class="fw-semibold">Foto Saat Ini:</p>
                            <img src="../uploads/<?= htmlspecialchars($project['gambar']) ?>?t=<?= time() ?>"
                                alt="Current Project Image" class="preview-image" id="currentImage"
                                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22200%22%3E%3Crect fill=%22%23eeeeee%22 width=%22300%22 height=%22200%22/%3E%3Ctext fill=%22%23999999%22 font-family=%22Arial%22 font-size=%2216%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ETidak Ada Gambar%3C/text%3E%3C/svg%3E'">
                        </div>
                    <?php endif; ?>
                    <div class="mt-3" id="newImagePreview" style="display: none;">
                        <p class="fw-semibold">Preview Foto Baru:</p>
                        <img src="" alt="New Project Image" class="preview-image" id="newImage">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Proyek</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 Politeknik Negeri Batam - Projek PBL IFPagi 1A-5</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('gambar').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('newImage').src = e.target.result;
                    document.getElementById('newImagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('newImagePreview').style.display = 'none';
            }
        });
    </script>
</body>

</html>