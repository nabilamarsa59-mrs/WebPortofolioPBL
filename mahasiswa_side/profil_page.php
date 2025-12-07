<?php
session_start();
require_once '../koneksi.php';
require_once '../auth_check.php'; // Pakai satpam pintar kita

// Ambil data mahasiswa yang sedang login
try {
    $sql_mahasiswa = "SELECT m.id, m.nama_lengkap, m.email, m.nim, m.jurusan, m.foto_profil
                      FROM mahasiswa m
                      WHERE m.id = (SELECT id_mahasiswa FROM users WHERE id = ?)";
    $stmt = $pdo->prepare($sql_mahasiswa);
    $stmt->execute([$_SESSION['user_id']]);
    $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mahasiswa) {
        die("Data mahasiswa tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Proses update profil
 $update_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];

    // Proses upload foto profil
    $nama_foto = $mahasiswa['foto_profil']; // Default ke foto lama
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = 'profil_' . $mahasiswa['nim'] . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            $nama_foto = $new_file_name;
        }
    }

    // Update database
    try {
        $sql_update = "UPDATE mahasiswa SET nama_lengkap = ?, email = ?, nim = ?, jurusan = ?, foto_profil = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nama, $email, $nim, $jurusan, $nama_foto, $mahasiswa['id']]);
        $update_message = "Profil berhasil diperbarui!";

        // Refresh data mahasiswa
        $stmt->execute([$_SESSION['user_id']]);
        $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $update_message = "Gagal memperbarui profil: " . $e->getMessage();
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: whitesmoke; padding-top: 80px; }
        .navbar { background-color: #002b5b !important; }
        .navbar a { color: #fff !important; text-decoration: none; margin-left: 15px; }
        .navbar a:hover { text-decoration: underline; }
        #previewFoto { width: 150px; height: 150px; object-fit: cover; cursor: pointer; }
        .card { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="home_mhs.php">WorkPiece</a>
            <div class="d-flex ms-auto">
                <a href="home_mhs.php" class="nav-link">Beranda</a>
                <a href="#" class="nav-link text-decoration-underline">Profil</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="container my-5">
        <?php if ($update_message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= $update_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- KIRI: Foto Profil -->
            <section class="col-md-4">
                <div class="card text-center shadow-sm p-4">
                    <h5 class="mb-3">Foto Profil</h5>
                    <img id="previewFoto" src="../uploads/<?= htmlspecialchars($mahasiswa['foto_profil'] ?? 'default-avatar.png') ?>"
                         alt="Foto Profil" class="rounded-circle mx-auto d-block border">
                    <div class="mt-3">
                        <input type="file" id="uploadFoto" name="foto_profil" accept="image/*" hidden>
                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('uploadFoto').click()">Ganti Foto</button>
                    </div>
                </div>
            </section>

            <!-- KANAN: Form Profil -->
            <section class="col-md-8">
                <div class="card shadow-sm p-4">
                    <h4 class="text-primary border-bottom pb-2 mb-3">Informasi Profil</h4>
                    <form action="profil_page.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($mahasiswa['nama_lengkap']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" name="nim" id="nim" class="form-control" value="<?= htmlspecialchars($mahasiswa['nim']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($mahasiswa['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="jurusan" class="form-label">Jurusan</label>
                            <select name="jurusan" id="jurusan" class="form-select" required>
                                <option value="Teknik Informatika" <?= $mahasiswa['jurusan'] == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
                                <option value="Teknik Elektro" <?= $mahasiswa['jurusan'] == 'Teknik Elektro' ? 'selected' : '' ?>>Teknik Elektro</option>
                                <option value="Teknik Mesin" <?= $mahasiswa['jurusan'] == 'Teknik Mesin' ? 'selected' : '' ?>>Teknik Mesin</option>
                                <option value="Manajemen dan Bisnis" <?= $mahasiswa['jurusan'] == 'Manajemen dan Bisnis' ? 'selected' : '' ?>>Manajemen dan Bisnis</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview foto saat dipilih
        document.getElementById('uploadFoto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewFoto').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>