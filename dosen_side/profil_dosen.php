<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    header("location:../login.php");
    exit();
}

// Get lecturer data from database
require_once '../koneksi.php';

try {
    $sql_dosen = "SELECT d.id, d.nama_lengkap, d.email, d.nidn, d.bidang_keahlian, d.foto_profil
                      FROM users u
                      JOIN dosen d ON u.id_dosen = d.id
                      WHERE u.id = ?";
    $stmt = $pdo->prepare($sql_dosen);
    $stmt->execute([$_SESSION['user_id']]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dosen) {
        die("Data dosen tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error saat mengambil data: " . $e->getMessage());
}

 $update_message = '';
 $update_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nidn = $_POST['nidn'];
    $bidang = $_POST['bidang'];

    $nama_foto = $dosen['foto_profil'];

    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = 'profil_dosen_' . $dosen['nidn'] . '_' . uniqid() . '.' . $file_ext;
        $upload_path = '../uploads/' . $new_file_name;

        if (move_uploaded_file($file_tmp, $upload_path)) {
            if (!empty($dosen['foto_profil']) && $dosen['foto_profil'] !== 'default-avatar.jpg' && file_exists('../uploads/' . $dosen['foto_profil'])) {
                unlink('../uploads/' . $dosen['foto_profil']);
            }
            $nama_foto = $new_file_name;
        } else {
            $update_error = "Gagal mengupload foto. Periksa folder 'uploads' dan izinnya.";
        }
    }

    if (empty($update_error)) {
        try {
            $sql_update = "UPDATE dosen SET nama_lengkap = ?, email = ?, nidn = ?, bidang_keahlian = ?, foto_profil = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$nama, $email, $nidn, $bidang, $nama_foto, $dosen['id']]);
            $update_message = "Profil berhasil diperbarui!";

            // Refresh data
            $stmt->execute([$_SESSION['user_id']]);
            $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Profil Dosen - WorkPiece</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: whitesmoke;
            padding-top: 100px;
        }

        .navbar {
            background: #00003c !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 1rem;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            padding-left: 10px;
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

        .navbar a:hover {
            text-decoration: underline;
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
            background-image: url('../uploads/default-avatar.jpg');
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

        .text-navy {
            color: #00003c !important;
        }

        .stat-number {
            font-size: 1.5rem;
            color: #00003c;
            font-weight: 600;
        }

        .activity-item {
            border-left: 4px solid #00003c;
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: transform 0.2s ease;
        }

        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .activity-item p {
            margin: 0;
        }

        .activity-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .toast-container {
            position: fixed;
            top: 110px;
            right: 20px;
            z-index: 1050;
        }

        .custom-toast {
            min-width: 250px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #003366;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .profile-info {
            transition: all 0.2s ease;
        }

        .profile-info:hover {
            background-color: rgba(0, 0, 60, 0.05);
            border-radius: 5px;
        }

        /* --- Footer --- */
        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin-top: 50px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="profil_dosen.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home_dosen.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <!-- Profile Photo Section - Modified to match student profile -->
                        <div class="profile-img-container">
                            <?php
                            $foto_path = '../uploads/default-avatar.jpg';
                            if (!empty($dosen['foto_profil'])) {
                                $foto_path = '../uploads/' . $dosen['foto_profil'];
                            }
                            ?>
                            <img id="previewFoto" src="<?= htmlspecialchars($foto_path) ?>?t=<?= time() ?>"
                                alt="Foto Profil">
                            <label for="uploadFoto" class="change-photo-btn">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                        </div>
                        <input type="file" id="uploadFoto" name="foto_profil" accept="image/*" style="display: none;">

                        <h5 class="text-navy mb-2 mt-3" id="dosen-name"><?= htmlspecialchars($dosen['nama_lengkap']) ?></h5>
                        <p class="text-muted small mb-1" id="dosen-nidn">NIDN: <?= htmlspecialchars($dosen['nidn']) ?></p>
                        <p class="text-muted small mb-4" id="dosen-bidang"><?= htmlspecialchars($dosen['bidang_keahlian']) ?></p>

                        <div class="border-top pt-3">
                            <div class="profile-info d-flex align-items-center mb-2 small">
                                <i class="bi bi-envelope text-navy me-2"></i>
                                <span class="text-muted" id="dosen-email"><?= htmlspecialchars($dosen['email']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="text-navy mb-3">Statistik Aktivitas</h6>

                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-check text-navy me-2"></i>
                                <span class="small text-muted">Portofolio Dinilai</span>
                            </div>
                            <span class="stat-number" id="stat-dinilai">0</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-chat-square-text text-navy me-2"></i>
                                <span class="small text-muted">Total Komentar</span>
                            </div>
                            <span class="stat-number" id="stat-komentar">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-navy mb-4">
                            <i class="bi bi-clock-history me-2"></i>
                            Aktivitas Terakhir
                        </h6>

                        <div id="activity-list">
                            <div class="activity-item">
                                <p class="small text-dark">Memuat aktivitas...</p>
                                <p class="activity-time">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>
    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 Politeknik Negeri Batam - Projek PBL IFPagi 1A-5</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            function showToast(message, type = 'success') {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();

                const toastHtml = `
            <div id="${toastId}" class="toast custom-toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
                toast.show();

                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            }

            fetch('get_aktivitas.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const activityList = document.getElementById('activity-list');
                        activityList.innerHTML = '';

                        data.data.forEach(activity => {
                            const activityHtml = `
                                <div class="activity-item">
                                    <p class="small text-dark">${activity.deskripsi}</p>
                                    <p class="activity-time">${activity.waktu}</p>
                                </div>
                            `;
                            activityList.insertAdjacentHTML('beforeend', activityHtml);
                        });

                        document.getElementById('stat-dinilai').textContent = data.data.length;
                        const komentarCount = data.data.filter(a => a.deskripsi.includes('komentar')).length;
                        document.getElementById('stat-komentar').textContent = komentarCount;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });

            // Profile photo upload functionality
            const uploadFoto = document.getElementById('uploadFoto');

            uploadFoto.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (file && file.type.startsWith('image/')) {
                    showLoading();

                    const formData = new FormData();
                    formData.append('foto_profil', file);

                    fetch('upload_foto.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            hideLoading();

                            if (data.success) {
                                document.getElementById('previewFoto').src = data.path + '?t=' + new Date().getTime();
                                showToast('Foto profil berhasil diperbarui!', 'success');
                            } else {
                                showToast(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            hideLoading();
                            console.error('Error:', error);
                            showToast('Terjadi kesalahan saat mengupload foto', 'danger');
                        });
                } else if (file) {
                    showToast('Silakan pilih file gambar yang valid.', 'danger');
                }
            });
        });
    </script>
</body>

</html>