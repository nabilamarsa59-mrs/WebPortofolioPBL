<?php
session_start();
require_once '../koneksi.php';

// Cek apakah user mahasiswa sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil data mahasiswa
 $stmt_mahasiswa = $pdo->prepare("SELECT id_mahasiswa FROM users WHERE id = ?");
 $stmt_mahasiswa->execute([$_SESSION['user_id']]);
 $id_mahasiswa = $stmt_mahasiswa->fetchColumn();

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $link = $_POST['link'];
    $tanggal = date('Y-m-d'); // Tanggal upload hari ini
    $nama_file = '';

    // Proses upload gambar
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid() . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            $nama_file = $new_file_name;
        }
    }

    // Simpan ke database
    $sql = "INSERT INTO projects (id_mahasiswa, judul, deskripsi, gambar, link_video, tanggal) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_mahasiswa, $judul, $deskripsi, $nama_file, $link, $tanggal]);

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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: #002b5b !important; }
        .navbar a { color: #fff !important; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        .form-section { border-bottom: 2px solid #e9ecef; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .upload-box { border: 2px dashed #ccc; border-radius: 8px; text-align: center; padding: 30px; cursor: pointer; background-color: #fafafa; }
        .upload-box:hover { background-color: #f1f7ff; border-color: #002b5b; }
    </style>
</head>
<body>
    <!-- Navbar (sederhana) -->
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

    <!-- Main Form -->
    <main class="container my-5">
        <div class="card shadow-lg p-4">
            <h2 class="text-primary">Tambah Proyek PBL</h2>
            <p class="text-muted mb-4">Isi informasi proyek Anda dengan lengkap</p>

            <!-- Form action ke file ini sendiri -->
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
                    <label for="link" class="form-label fw-semibold">Link Video (Opsional)</label>
                    <input type="url" name="link" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label fw-semibold">Foto Proyek</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Proyek</button>
            </form>
        </div>
    </main>
</body>
</html>