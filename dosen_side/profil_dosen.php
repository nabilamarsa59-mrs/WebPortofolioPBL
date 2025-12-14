<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    header("location:../login.php");
    exit();
}

require_once '../koneksi.php';

// Ambil data dosen
$sql = "SELECT * FROM dosen WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['id_dosen']]);
$dosen = $stmt->fetch();

// Ambil statistik
$sql_stats = "SELECT 
                COUNT(*) as total_projects,
                COUNT(CASE WHEN p.nilai IS NOT NULL THEN 1 END) as projects_graded,
                COUNT(DISTINCT p.id_mahasiswa) as unique_students
              FROM projects p
              LEFT JOIN penilaian n ON p.id = n.id_project
              WHERE n.id_dosen = ? OR n.id_dosen IS NULL";
$stmt_stats = $pdo->prepare($sql_stats);
$stmt_stats->execute([$_SESSION['id_dosen']]);
$stats = $stmt_stats->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen - WorkPiece</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            padding-top: 100px;
        }
        .navbar {
            background: rgba(0, 0, 60, 0.8) !important;
        }
        .profile-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        .profile-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .change-photo-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            padding: 0;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home_dosen.php">WorkPiece</a>
            <div class="collapse navbar-collapse justify-content-end">
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

    <div class="container mt-4">
        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="profile-container">
                            <?php if ($dosen['foto_profil']): ?>
                                <img src="../<?= htmlspecialchars($dosen['foto_profil']) ?>" 
                                     class="profile-img" 
                                     alt="Foto Profil"
                                     id="profileImage">
                            <?php else: ?>
                                <div class="profile-img bg-primary d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-primary change-photo-btn" id="changePhotoBtn">
                                <i class="bi bi-camera-fill text-white"></i>
                            </button>
                        </div>
                        <input type="file" id="photoInput" accept="image/*" style="display: none;">
                        
                        <h5 class="mt-3"><?= htmlspecialchars($dosen['nama_lengkap']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($dosen['jabatan']) ?></p>
                        <p class="text-muted small">NIDN: <?= htmlspecialchars($dosen['nidn']) ?></p>
                        
                        <div class="mt-3">
                            <p><i class="bi bi-envelope me-2"></i> <?= htmlspecialchars($dosen['email']) ?></p>
                            <p><i class="bi bi-gear me-2"></i> <?= htmlspecialchars($dosen['bidang_keahlian']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="mb-3">Statistik</h6>
                        <div class="d-flex justify-content-between py-2">
                            <span>Proyek Dinilai</span>
                            <strong><?= $stats['projects_graded'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Total Proyek</span>
                            <strong><?= $stats['total_projects'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Mahasiswa Dinilai</span>
                            <strong><?= $stats['unique_students'] ?? 0 ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity & Bio -->
            <div class="col-lg-8">
                <!-- Bio -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="mb-3">Biografi</h6>
                        <p><?= nl2br(htmlspecialchars($dosen['bio'] ?? 'Belum ada biografi')) ?></p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-3">Aktivitas Terakhir</h6>
                        <div id="activityList">
                            <!-- Aktivitas akan dimuat via JavaScript -->
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load aktivitas
            loadActivities();
            
            // Fungsi untuk toast
            function showToast(message, type = 'success') {
                const toastHtml = `
                    <div class="toast align-items-center text-white bg-${type} border-0">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                const container = document.querySelector('.toast-container');
                container.insertAdjacentHTML('beforeend', toastHtml);
                const toast = new bootstrap.Toast(container.lastElementChild, {autohide: true, delay: 3000});
                toast.show();
            }
            
            // Load aktivitas
            function loadActivities() {
                fetch('get_aktivitas.php')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('activityList');
                        if (data.success && data.data.length > 0) {
                            let html = '';
                            data.data.forEach(activity => {
                                html += `
                                    <div class="border-start border-primary ps-3 mb-3">
                                        <p class="mb-1">${activity.deskripsi}</p>
                                        <small class="text-muted">${activity.waktu}</small>
                                    </div>
                                `;
                            });
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<p class="text-muted">Belum ada aktivitas</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
            
            // Upload foto
            document.getElementById('changePhotoBtn').addEventListener('click', function() {
                document.getElementById('photoInput').click();
            });
            
            document.getElementById('photoInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                if (!file.type.startsWith('image/')) {
                    showToast('Hanya file gambar yang diizinkan', 'danger');
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    showToast('Ukuran file maksimal 5MB', 'danger');
                    return;
                }
                
                const formData = new FormData();
                formData.append('foto_profil', file);
                
                document.getElementById('loadingOverlay').style.display = 'flex';
                
                fetch('upload_foto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    if (data.success) {
                        // Update gambar profil
                        const img = document.getElementById('profileImage');
                        if (img) {
                            img.src = '../' + data.path + '?t=' + new Date().getTime();
                        } else {
                            location.reload();
                        }
                        showToast('Foto profil berhasil diperbarui');
                    } else {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(error => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'danger');
                });
            });
        });
    </script>
</body>
</html>