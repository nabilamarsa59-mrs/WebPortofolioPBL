<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user login dan rolenya mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil data mahasiswa saat ini
try {
    $sql_mahasiswa = "SELECT m.id, m.nama_lengkap, m.email, m.nim, m.jurusan, m.foto_profil
                      FROM users u
                      JOIN mahasiswa m ON u.id_mahasiswa = m.id
                      WHERE u.id = ?";
    $stmt = $pdo->prepare($sql_mahasiswa);
    $stmt->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mahasiswa) {
        die("Data mahasiswa tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error saat mengambil data: " . $e->getMessage());
}

// Variabel untuk notifikasi
$update_message = '';
$update_error = '';

// Logika Handle Update Profil (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];

    // Default pakai foto lama jika tidak upload baru
    $nama_foto = $mahasiswa['foto_profil'];

    // Handle Upload Foto Baru
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = 'profil_' . $mahasiswa['nim'] . '_' . uniqid() . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Hapus foto lama jika ada (kecuali foto default)
            if (!empty($mahasiswa['foto_profil']) && $mahasiswa['foto_profil'] !== 'default-avatar.jpg' && file_exists('../uploads/' . $mahasiswa['foto_profil'])) {
                unlink('../uploads/' . $mahasiswa['foto_profil']);
            }
            $nama_foto = $new_file_name;
        } else {
            $update_error = "Gagal mengupload foto. Periksa folder 'uploads' dan izinnya.";
        }
    }

    // Update ke Database jika tidak ada error upload
    if (empty($update_error)) {
        try {
            $sql_update = "UPDATE mahasiswa
                           SET nama_lengkap = ?, email = ?, nim = ?, jurusan = ?, foto_profil = ?
                           WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$nama, $email, $nim, $jurusan, $nama_foto, $mahasiswa['id']]);

            $update_message = "Profil berhasil diperbarui!";

            // Refresh data mahasiswa agar tampilan diupdate
            $stmt->execute([$_SESSION['user_id']]);
            $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $update_error = "Gagal memperbarui profil: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa - WorkPiece</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* --- Global Styles --- */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            padding-top: 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        /* --- Navbar --- */
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

        /* --- Components --- */
        .profile-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            border: none;
        }

        .profile-img-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto;
        }

        #previewFoto {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #f0f0f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #e0e0e0;
        }

        .change-photo-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s;
            border: 3px solid white;
        }

        .change-photo-btn:hover {
            background-color: #0056b3;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: grey;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            font-weight: bold;
            border: 5px solid #f0f0f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* --- Footer --- */
        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin-top: auto;
            width: 100%;
        }

        /* --- Responsive --- */
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

            .profile-img-container {
                width: 150px;
                height: 150px;
            }

            .avatar-placeholder {
                font-size: 3rem;
            }

            .profile-card {
                margin-bottom: 1rem;
            }

            .row.g-4 {
                gap: 1rem !important;
            }

            .profile-img {
                width: 35px;
                height: 35px;
            }
        }

        @media (max-width: 575.98px) {
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .profile-img-container {
                width: 120px;
                height: 120px;
            }

            .avatar-placeholder {
                font-size: 2.5rem;
            }

            .change-photo-btn {
                width: 35px;
                height: 35px;
                font-size: 0.875rem;
            }

            .card-body {
                padding: 1rem;
            }

            h4,
            h5 {
                font-size: 1.1rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .btn {
                font-size: 0.9rem;
            }

            .dropdown-toggle {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body class="bg-light">
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

                            <!-- Profil Image -->
                            <?php if (!empty($mahasiswa['foto_profil'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($mahasiswa['foto_profil']) ?>?t=<?= time() ?>"
                                    class="profile-img" alt="Profile">
                            <?php else: ?>
                                <i class="bi bi-person-circle fs-4 me-1"></i>
                            <?php endif; ?>

                            <span class="d-none d-md-inline"><?= htmlspecialchars($mahasiswa['nama_lengkap']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profil_page.php"><i class="bi bi-person me-2"></i>Profil
                                    Saya</a></li>
                            <li><a class="dropdown-item" href="upload_project.php"><i
                                        class="bi bi-plus-circle me-2"></i>Tambah Proyek Baru</a></li>
                            <li><a class="dropdown-item" href="home_mhs.php"><i
                                        class="bi bi-arrow-left me-1"></i>Beranda</a></li>
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

    <!-- Main Container -->
    <main class="container my-5">

        <!-- Notifikasi Sukses -->
        <?php if ($update_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= $update_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Error -->
        <?php if ($update_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $update_error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 align-items-md-center">
            <!-- Kolom Kiri: Foto Profil -->
            <section class="col-12 col-md-4 text-center">
                <div class="profile-card card shadow-sm p-4">
                    <h5 class="mb-4">Foto Profil</h5>
                    <div class="profile-img-container">
                        <?php
                        // Logika Menampilkan Foto atau Inisial
                        $foto_path = '';
                        $show_placeholder = true;

                        if (!empty($mahasiswa['foto_profil'])) {
                            $foto_path = '../uploads/' . $mahasiswa['foto_profil'];
                            if (file_exists($foto_path)) {
                                $show_placeholder = false;
                            }
                        }

                        if ($show_placeholder) {
                            $initials = strtoupper(substr($mahasiswa['nama_lengkap'], 0, 2));
                            echo '<div class="avatar-placeholder" id="previewFoto">' . $initials . '</div>';
                        } else {
                            echo '<img id="previewFoto" src="' . htmlspecialchars($foto_path) . '?t=' . time() . '" alt="Foto Profil">';
                        }
                        ?>

                        <!-- Tombol Upload Tersembunyi (Ditrigger Label) -->
                        <label for="uploadFoto" class="change-photo-btn" title="Ganti Foto">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>
                    <p class="mt-3 text-muted small">Foto opsional. Klik ikon kamera untuk mengganti.</p>
                </div>
            </section>

            <!-- Kolom Kanan: Form Edit -->
            <section class="col-12 col-md-8">
                <div class="profile-card card shadow-sm p-4">
                    <h4 class="text-primary border-bottom pb-3 mb-4">Informasi Profil</h4>
                    <form action="profil_page.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="<?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nim" class="form-label">NIM</label>
                                <input type="text" name="nim" id="nim" class="form-control"
                                    value="<?= htmlspecialchars($mahasiswa['nim']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="<?= htmlspecialchars($mahasiswa['email']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="jurusan" class="form-label">Jurusan</label>
                                <select name="jurusan" id="jurusan" class="form-select" required>
                                    <option value="Teknik Informatika" <?= $mahasiswa['jurusan'] == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
                                    <option value="Teknik Elektro" <?= $mahasiswa['jurusan'] == 'Teknik Elektro' ? 'selected' : '' ?>>Teknik Elektro</option>
                                    <option value="Teknik Mesin" <?= $mahasiswa['jurusan'] == 'Teknik Mesin' ? 'selected' : '' ?>>Teknik Mesin</option>
                                    <option value="Manajemen dan Bisnis" <?= $mahasiswa['jurusan'] == 'Manajemen dan Bisnis' ? 'selected' : '' ?>>Manajemen dan Bisnis</option>
                                </select>
                            </div>
                        </div>

                        <!-- Input File Tersembunyi -->
                        <input type="file" id="uploadFoto" name="foto_profil" accept="image/*" hidden>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </section>
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
        // Logic Preview Foto saat dipilih
        document.getElementById('uploadFoto').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewContainer = document.querySelector('.profile-img-container');
                    // Ganti elemen preview (bisa img atau div placeholder) menjadi img baru
                    previewContainer.querySelector('#previewFoto').outerHTML =
                        `<img id="previewFoto" src="${e.target.result}" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 5px solid #f0f0f0; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>