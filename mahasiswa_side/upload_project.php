<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user mahasiswa sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil data mahasiswa untuk mendapatkan id_mahasiswa
$stmt_mahasiswa = $pdo->prepare("SELECT id_mahasiswa FROM users WHERE id = ?");
$stmt_mahasiswa->execute([$_SESSION['user_id']]);
$id_mahasiswa = $stmt_mahasiswa->fetchColumn();

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $link_video = $_POST['link_video']; // Hanya ambil link video
    $tanggal = date('Y-m-d');
    $nama_file = '';

    // KODE BARU UNTUK DEBUGGING
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
    // Simpan ke database (QUERY LEBIH SEDERHANA)
    $sql = "INSERT INTO projects (id_mahasiswa, judul, deskripsi, kategori, tanggal, gambar, link_demo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_mahasiswa, $judul, $deskripsi, $kategori, $tanggal, $nama_file, $link_video]);

    // Arahkan ke halaman home mahasiswa
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: #002b5b !important;
        }

        .navbar a {
            color: #fff !important;
            text-decoration: none;
            margin-left: 15px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark px-5">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="home_mhs.php">WorkPiece</a>
            <div class="d-flex">
                <a href="home_mhs.php" class="nav-link">Beranda</a>
                <a href="profil_page.php" class="nav-link">Profil</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-primary">Tambah Proyek PBL</h2>
            <p class="text-muted mb-4">Isi informasi proyek Anda dengan lengkap</p>

            <form action="upload_project.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="judul" class="form-label fw-semibold">Judul Proyek</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi Proyek</label>
                    <textarea name="deskripsi" rows="4" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="kategori" class="form-label fw-semibold">Kategori Proyek</label>
                    <input type="text" name="kategori" class="form-control"
                        placeholder="Contoh: Aplikasi Web, IoT, Desain Manufaktur" required>
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
                <button type="submit" class="btn btn-primary w-100">Simpan Proyek</button>
            </form>
        </div>
    </main>
</body>

</html>