<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID project dari URL
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

// Ambil data mahasiswa untuk verifikasi
$stmt_mahasiswa = $pdo->prepare("SELECT id_mahasiswa FROM users WHERE id = ?");
$stmt_mahasiswa->execute([$_SESSION['user_id']]);
$id_mahasiswa = $stmt_mahasiswa->fetchColumn();

// Pastikan project milik mahasiswa yang login
if ($project['id_mahasiswa'] != $id_mahasiswa) {
    header("Location: home_mhs.php");
    exit();
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $kategori = $_POST['kategori'];
    $link_video = $_POST['link_video'];

    $nama_file_gambar = $project['gambar'];

    // Proses upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_name = $_FILES['gambar']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = uniqid('project_', true) . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            // Hapus gambar lama jika ada
            if (!empty($project['gambar']) && file_exists('../uploads/' . $project['gambar'])) {
                unlink('../uploads/' . $project['gambar']);
            }

            // Update dengan gambar baru (SIMPAN HANYA NAMA FILE)
            $nama_file_gambar = $new_file_name;
        }
    }

    // Update database
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background: #00003c !important;
            padding: 0.75rem 0;
            z-index: 1000;
        }

        .navbar a {
            color: #fff !important;
            text-decoration: none;
            margin-left: 15px;
        }

        .navbar a:hover {
            color: #00ffff !important;
        }

        .preview-image {
            max-width: 300px;
            max-height: 300px;
            border-radius: 10px;
            margin-top: 10px;
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
            <h2 class="text-primary">Edit Proyek PBL</h2>
            <p class="text-muted mb-4">Perbarui informasi proyek Anda</p>

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
                    <input type="text" name="kategori" class="form-control"
                        value="<?= htmlspecialchars($project['kategori']) ?>" required>
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

                    <!-- Preview Gambar Lama -->
                    <?php if (!empty($project['gambar'])): ?>
                        <div class="mt-3">
                            <p class="fw-semibold">Foto Saat Ini:</p>
                            <img src="../uploads/<?= htmlspecialchars($project['gambar']) ?>?t=<?= time() ?>"
                                alt="Current Project Image"
                                class="preview-image"
                                id="currentImage"
                                onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22200%22%3E%3Crect fill=%22%23eeeeee%22 width=%22300%22 height=%22200%22/%3E%3Ctext fill=%22%23999999%22 font-family=%22Arial%22 font-size=%2216%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ETidak Ada Gambar%3C/text%3E%3C/svg%3E'">
                        </div>
                    <?php endif; ?>

                    <!-- Preview Gambar Baru -->
                    <div class="mt-3" id="newImagePreview" style="display: none;">
                        <p class="fw-semibold">Preview Foto Baru:</p>
                        <img src="" alt="New Project Image" class="preview-image" id="newImage">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update Proyek</button>
            </form>
        </div>
    </main>

    <script>
        // Preview gambar baru saat dipilih
        document.getElementById('gambar').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
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