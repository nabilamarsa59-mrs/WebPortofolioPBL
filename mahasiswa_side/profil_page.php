<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

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

$update_message = '';
$update_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];

    // Default-kan nama foto ke foto yang sudah ada di database
    $nama_foto = $mahasiswa['foto_profil'];

    // Proses upload foto profil HANYA JIKA ADA FILE BARU
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = 'profil_' . $mahasiswa['nim'] . '_' . uniqid() . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Jika berhasil, hapus foto lama (jika ada dan bukan default)
            if (!empty($mahasiswa['foto_profil']) && $mahasiswa['foto_profil'] !== 'default-avatar.jpg' && file_exists('../uploads/' . $mahasiswa['foto_profil'])) {
                unlink('../uploads/' . $mahasiswa['foto_profil']);
            }
            // Gunakan nama file baru (SIMPAN HANYA NAMA FILE, BUKAN PATH LENGKAP)
            $nama_foto = $new_file_name;
        } else {
            $update_error = "Gagal mengupload foto. Periksa folder 'uploads' dan izinnya.";
        }
    }

    // Update database hanya jika tidak ada error upload
    if (empty($update_error)) {
        try {
            $sql_update = "UPDATE mahasiswa SET nama_lengkap = ?, email = ?, nim = ?, jurusan = ?, foto_profil = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$nama, $email, $nim, $jurusan, $nama_foto, $mahasiswa['id']]);
            $update_message = "Profil berhasil diperbarui!";

            // Refresh data mahasiswa untuk menampilkan perubahan
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
    <title>Profil - WorkPiece</title>
    <!-- Google Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            padding-top: 80px;
        }

        .navbar {
            background: #00003c !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            /* --- Perubahan --- */
            padding: 0.75rem 1rem;
            /* Ditambah padding horizontal agar tidak mepet */
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            /* Memisahkan item kiri dan kanan */
            align-items: center;
            /* Menyelaraskan item secara vertikal (ini penting untuk meratakan foto profil dan teks) */
        }

        /* --- Perubahan --- */
        .navbar-brand {
            font-weight: bold;
            /* Menebalkan teks "WorkPiece" */
            font-size: 1.5rem;
            /* Membesarkan ukuran font agar lebih menonjol */
        }

        /* --- Perubahan --- */
        .navbar-nav {
            /* Menyelaraskan item di dalam navbar (Dashboard & Profil) secara vertikal */
            align-items: center;
        }

        /* --- Perubahan --- */
        .navbar-nav .nav-item {
            /* Memberi jarak antar item di navbar sebelah kanan */
            margin-left: 15px;
        }

        .navbar-nav .nav-item:first-child {
            /* Menghilangkan margin kiri untuk item pertama agar tidak terlalu menjorok ke dalam */
            margin-left: 0;
        }

        .navbar-brand,
        .navbar-nav .nav-link,
        .dropdown-item {
            color: #fff !important;
        }

        /* --- Perubahan --- */
        .navbar-nav .nav-link {
            font-weight: bold;
            /* Menebalkan teks "Dashboard" */
        }

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
            background-image: url('profil.jpeg');
            background-size: cover;
            background-position: center;
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
        }

        .change-photo-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand ms-3" href="home_mhs.php">WorkPiece</a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="home_mhs.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active text-decoration-underline" href="#">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container my-5">
        <!-- Tampilkan Pesan Sukses atau Error -->
        <?php if ($update_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= $update_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($update_error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $update_error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 align-items-md-center">
            <!-- KIRI: Foto Profil -->
            <section class="col-md-4 text-center">
                <div class="profile-card card shadow-sm p-4">
                    <h5 class="mb-4">Foto Profil</h5>
                    <div class="profile-img-container">
                        <!-- PERBAIKAN: Path foto yang benar -->
                        <?php
                        $foto_path = '../uploads/default-avatar.jpg'; // default
                        if (!empty($mahasiswa['foto_profil'])) {
                            $foto_path = '../uploads/' . $mahasiswa['foto_profil'];
                        }
                        ?>
                        <img id="previewFoto" src="<?= htmlspecialchars($foto_path) ?>?t=<?= time() ?>"
                            alt="Foto Profil">
                        <label for="uploadFoto" class="change-photo-btn">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>
                    <p class="mt-3 text-muted small">Foto opsional. Klik ikon kamera untuk mengganti.</p>
                </div>
            </section>

            <!-- KANAN: Form Profil -->
            <section class="col-md-8">
                <div class="profile-card card shadow-sm p-4">
                    <h4 class="text-primary border-bottom pb-3 mb-4">Informasi Profil</h4>
                    <form action="profil_page.php" method="post" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" id="nama" class="form-control"
                                    value="<?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>" required>
                            </div>
                            <div class="col-md-6">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview foto saat dipilih
        document.getElementById('uploadFoto').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('previewFoto').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>