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

$stmt_mahasiswa = $pdo->prepare("SELECT m.id, m.jurusan FROM users u JOIN mahasiswa m ON u.id_mahasiswa = m.id WHERE u.id = ?");
$stmt_mahasiswa->execute([$_SESSION['user_id']]);
$mahasiswa_data = $stmt_mahasiswa->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa_data) {
    die("Data mahasiswa tidak ditemukan.");
}

$id_mahasiswa = $mahasiswa_data['id'];
$jurusan_mahasiswa = $mahasiswa_data['jurusan'];

// Ambil kategori sesuai jurusan mahasiswa
$all_kategori = [];
try {
    $stmt_kategori = $pdo->prepare("SELECT id, nama_kategori FROM kategori_proyek WHERE jurusan = ? ORDER BY nama_kategori ASC");
    $stmt_kategori->execute([$jurusan_mahasiswa]);
    $all_kategori = $stmt_kategori->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $all_kategori = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $link_video = $_POST['link_video'];
    $tanggal = date('Y-m-d');
    $nama_file = '';

    $tipe_kategori = $_POST['tipe_kategori'];
    if ($tipe_kategori === 'lainnya') {
        $kategori_baru = trim($_POST['kategori_lainnya']);
        
        // Insert kategori baru dengan jurusan mahasiswa
        $stmt_insert_kategori = $pdo->prepare("INSERT INTO kategori_proyek (nama_kategori, jurusan) VALUES (?, ?)");
        $stmt_insert_kategori->execute([$kategori_baru, $jurusan_mahasiswa]);
        $kategori = $pdo->lastInsertId();
    } else {
        $kategori = $_POST['id_kategori'];
    }

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid('project_', true) . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            $nama_file = $new_file_name;
        }
    }

    $sql = "INSERT INTO projects (id_mahasiswa, judul, deskripsi, kategori, tanggal, gambar, link_demo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_mahasiswa, $judul, $deskripsi, $kategori, $tanggal, $nama_file, $link_video]);

    header("Location: home_mhs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Proyek - WorkPiece</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
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

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin-top: auto;
            width: 100%;
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

            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
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

            h2 {
                font-size: 1.3rem;
            }

            p {
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

    <main class="container my-5">
        <div class="card shadow-lg p-3 p-md-4">
            <h2 class="text-primary mb-2">Tambah Proyek PBL</h2>
            <p class="text-muted mb-3">Isi informasi proyek Anda dengan lengkap</p>

            <div class="info-box">
                <strong>Info Jurusan:</strong> <?= htmlspecialchars($jurusan_mahasiswa) ?><br>
                <small class="text-muted">Kategori proyek yang tersedia disesuaikan dengan jurusan Anda</small>
            </div>

            <form action="upload_project.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="judul" class="form-label fw-semibold">Judul Proyek</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi Proyek</label>
                    <textarea name="deskripsi" rows="4" class="form-control" required></textarea>
                </div>

                <!-- Dropdown Kategori dengan Opsi "Lainnya" -->
                <div class="mb-3">
                    <label for="id_kategori" class="form-label fw-semibold">Kategori Proyek</label>
                    <select name="id_kategori" id="id_kategori" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <?php if (empty($all_kategori)): ?>
                            <option value="" disabled>Tidak ada kategori untuk jurusan Anda</option>
                        <?php else: ?>
                            <?php foreach ($all_kategori as $kategori): ?>
                                <option value="<?= $kategori['id'] ?>">
                                    <?= htmlspecialchars($kategori['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="lainnya">Lainnya (Tulis Kategori Baru)</option>
                    </select>
                    <input type="text" name="kategori_lainnya" id="kategori_lainnya" class="form-control mt-2"
                        placeholder="Tulis kategori baru..." style="display: none;">
                    <input type="hidden" name="tipe_kategori" id="tipe_kategori" value="pilihan">
                </div>

                <div class="mb-3">
                    <label for="link_video" class="form-label fw-semibold">Link Video YouTube (Opsional)</label>
                    <input type="url" name="link_video" class="form-control"
                        placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="mb-3">
                    <label for="gambar" class="form-label fw-semibold">Foto/Gambar Proyek</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Proyek</button>
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
        document.addEventListener('DOMContentLoaded', function () {
            const kategoriSelect = document.getElementById('id_kategori');
            const kategoriLainnya = document.getElementById('kategori_lainnya');
            const tipeKategoriInput = document.getElementById('tipe_kategori');

            kategoriSelect.addEventListener('change', function () {
                const selectedValue = this.value;

                if (selectedValue === 'lainnya') {
                    kategoriLainnya.style.display = 'block';
                    kategoriLainnya.required = true;
                    tipeKategoriInput.value = 'lainnya';
                } else {
                    kategoriLainnya.style.display = 'none';
                    kategoriLainnya.required = false;
                    kategoriLainnya.value = '';
                    tipeKategoriInput.value = 'pilihan';
                }
            });
        });
    </script>
</body>

</html>