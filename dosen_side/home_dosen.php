<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    header("location:../login.php");
    exit();
}

require_once '../koneksi.php';

// --- KODE UNTUK MENGAMBIL DATA ---
$sql = "SELECT
           p.id,
           p.judul,
           p.deskripsi,
           p.kategori,
           p.tanggal,
           p.gambar,
           p.link_demo,
           m.nama_lengkap,
           m.nim,
           m.jurusan,
           pen.nilai,
           pen.komentar,
           pen.tanggal_dinilai
        FROM projects p
        JOIN mahasiswa m ON p.id_mahasiswa = m.id
        LEFT JOIN penilaian pen ON p.id = pen.id_project
        ORDER BY p.tanggal DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPiece - Dashboard Dosen</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* [CSS yang sama dari kode Anda - tidak diubah] */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
            padding-top: 100px;
        }
        .navbar {
            background: rgba(0, 0, 60, 0.8) !important;
        }
        /* ... tambahkan semua CSS Anda yang lain ... */
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home_dosen.php">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="profil_dosen.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="home_dosen.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container mt-4">
            <div class="row">
                <div class="col-12">
                    <h2 class="mt-4 mb-3 text-navy">Dashboard Penilaian Proyek Mahasiswa</h2>
                    
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <label class="me-2 mb-0">Filter:</label>
                                    <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                        <option value="all">Semua Status</option>
                                        <option value="belum-dinilai">Belum Dinilai</option>
                                        <option value="sudah-dinilai">Sudah Dinilai</option>
                                    </select>
                                    <select class="form-select form-select-sm" id="jurusanFilter" style="width: auto;">
                                        <option value="all">Semua Jurusan</option>
                                        <?php
                                        // Ambil jurusan unik
                                        $sql_jurusan = "SELECT DISTINCT jurusan FROM mahasiswa WHERE jurusan IS NOT NULL";
                                        $stmt_jurusan = $pdo->query($sql_jurusan);
                                        while ($jurusan = $stmt_jurusan->fetch()) {
                                            echo '<option value="' . htmlspecialchars($jurusan['jurusan']) . '">' . htmlspecialchars($jurusan['jurusan']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <select class="form-select form-select-sm" id="kategoriFilter" style="width: auto;">
                                        <option value="all">Semua Kategori</option>
                                        <?php
                                        // Ambil kategori unik dari tabel projects
                                        $sql_kategori = "SELECT DISTINCT kategori FROM projects WHERE kategori IS NOT NULL";
                                        $stmt_kategori = $pdo->query($sql_kategori);
                                        while ($kategori = $stmt_kategori->fetch()) {
                                            echo '<option value="' . htmlspecialchars($kategori['kategori']) . '">' . htmlspecialchars($kategori['kategori']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary" id="resetFilters">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted">Menampilkan <span id="projectCount"><?= count($projects) ?></span> proyek</span>
                            </div>
                        </div>
                    </div>

                    <!-- Project Cards -->
                    <div class="row" id="projectList">
                        <?php if (empty($projects)): ?>
                            <div class="col-12">
                                <p class="text-center mt-4">Belum ada proyek mahasiswa yang diajukan.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($projects as $project): ?>
                                <div class="col-md-6 col-lg-4 project-item"
                                    data-status="<?= $project['nilai'] ? 'sudah-dinilai' : 'belum-dinilai' ?>"
                                    data-jurusan="<?= htmlspecialchars($project['jurusan']) ?>"
                                    data-kategori="<?= htmlspecialchars($project['kategori']) ?>"
                                    data-nim="<?= htmlspecialchars($project['nim']) ?>"
                                    data-student-name="<?= htmlspecialchars($project['nama_lengkap']) ?>">
                                    <div class="card project-card h-100">
                                        <?php if ($project['gambar']): ?>
                                            <img src="../uploads/<?= htmlspecialchars($project['gambar']) ?>" 
                                                 class="card-img-top" 
                                                 alt="Project Image"
                                                 style="height: 200px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                 style="height: 200px;">
                                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= htmlspecialchars($project['judul']) ?></h5>
                                            <p class="card-text text-muted small">Oleh: 
                                                <?= htmlspecialchars($project['nama_lengkap']) ?>
                                                (<?= htmlspecialchars($project['nim']) ?>)
                                            </p>
                                            <p class="card-text small text-info mb-2">
                                                <i class="bi bi-building me-1"></i>
                                                <?= htmlspecialchars($project['jurusan']) ?>
                                            </p>
                                            <p class="card-text">
                                                <?= substr(htmlspecialchars($project['deskripsi']), 0, 100) . '...'; ?>
                                            </p>
                                            <div class="mt-auto">
                                                <?php if ($project['nilai']): ?>
                                                    <span class="badge bg-success status-badge">Sudah Dinilai 
                                                        (<?= htmlspecialchars($project['nilai']) ?>)
                                                    </span>
                                                    <button class="btn btn-secondary btn-sm float-end" 
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#gradeModal" 
                                                            data-id="<?= $project['id'] ?>"
                                                            data-nilai="<?= htmlspecialchars($project['nilai']) ?>"
                                                            data-komentar="<?= htmlspecialchars($project['komentar'] ?? '') ?>">
                                                        <i class="bi bi-eye"></i> Lihat Detail
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-warning status-badge">Belum Dinilai</span>
                                                    <button class="btn btn-primary btn-sm float-end" 
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#gradeModal" 
                                                            data-id="<?= $project['id'] ?>">
                                                        <i class="bi bi-pencil-square"></i> Beri Nilai
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Penilaian -->
    <div class="modal fade" id="gradeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Penilaian Proyek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="projectDetails"></div>
                    <hr>
                    <form id="gradeForm">
                        <input type="hidden" id="projectId" name="id_project">
                        <div class="mb-3">
                            <label for="gradeSelect" class="form-label">Nilai</label>
                            <select class="form-select" id="gradeSelect" name="nilai" required>
                                <option value="">-- Pilih Nilai --</option>
                                <option value="A">A (Sangat Baik)</option>
                                <option value="A-">A- (Sangat Baik)</option>
                                <option value="B+">B+ (Baik)</option>
                                <option value="B">B (Baik)</option>
                                <option value="B-">B- (Cukup Baik)</option>
                                <option value="C+">C+ (Cukup)</option>
                                <option value="C">C (Cukup)</option>
                                <option value="D">D (Kurang)</option>
                                <option value="E">E (Sangat Kurang)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="commentText" class="form-label">Komentar / Feedback</label>
                            <textarea class="form-control" id="commentText" name="komentar" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success" id="saveGradeBtn">
                        <i class="bi bi-check-circle"></i> Simpan Penilaian
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk menampilkan toast
            function showToast(message, type = 'success') {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();
                
                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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

            // Fungsi untuk loading
            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }
            
            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            // Filter
            function applyFilters() {
                const statusFilter = document.getElementById('statusFilter').value;
                const jurusanFilter = document.getElementById('jurusanFilter').value;
                const kategoriFilter = document.getElementById('kategoriFilter').value;
                
                const projectItems = document.querySelectorAll('.project-item');
                let visibleCount = 0;
                
                projectItems.forEach(item => {
                    const status = item.dataset.status;
                    const jurusan = item.dataset.jurusan;
                    const kategori = item.dataset.kategori;
                    
                    let showItem = true;
                    
                    if (statusFilter !== 'all' && status !== statusFilter) showItem = false;
                    if (jurusanFilter !== 'all' && jurusan !== jurusanFilter) showItem = false;
                    if (kategoriFilter !== 'all' && kategori !== kategoriFilter) showItem = false;
                    
                    if (showItem) {
                        item.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        item.classList.add('d-none');
                    }
                });
                
                document.getElementById('projectCount').textContent = visibleCount;
            }

            // Event listener untuk filter
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('jurusanFilter').addEventListener('change', applyFilters);
            document.getElementById('kategoriFilter').addEventListener('change', applyFilters);
            
            document.getElementById('resetFilters').addEventListener('click', function() {
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('jurusanFilter').value = 'all';
                document.getElementById('kategoriFilter').value = 'all';
                applyFilters();
                showToast('Filter telah direset');
            });

            // Modal event
            document.getElementById('gradeModal').addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const projectId = button.getAttribute('data-id');
                const existingNilai = button.getAttribute('data-nilai');
                const existingKomentar = button.getAttribute('data-komentar');
                
                document.getElementById('projectId').value = projectId;
                
                // Ambil detail proyek via AJAX
                showLoading();
                fetch(`get_project_detail.php?id=${projectId}`)
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            const project = data.data;
                            const detailsHtml = `
                                <h6>${project.judul}</h6>
                                <p><strong>Mahasiswa:</strong> ${project.nama_lengkap} (${project.nim})</p>
                                <p><strong>Jurusan:</strong> ${project.jurusan}</p>
                                <p><strong>Kategori:</strong> ${project.kategori}</p>
                                <p><strong>Deskripsi:</strong> ${project.deskripsi}</p>
                                ${project.gambar ? `<img src="../uploads/${project.gambar}" class="img-fluid mb-3" alt="Project Image">` : ''}
                            `;
                            document.getElementById('projectDetails').innerHTML = detailsHtml;
                            
                            // Set nilai dan komentar jika sudah ada
                            if (existingNilai) {
                                document.getElementById('gradeSelect').value = existingNilai;
                                document.getElementById('commentText').value = existingKomentar;
                            } else {
                                document.getElementById('gradeForm').reset();
                            }
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                    });
            });

            // Simpan penilaian
            document.getElementById('saveGradeBtn').addEventListener('click', function() {
                const form = document.getElementById('gradeForm');
                const formData = new FormData(form);
                
                if (!formData.get('nilai')) {
                    showToast('Silakan pilih nilai terlebih dahulu', 'danger');
                    return;
                }
                
                showLoading();
                
                fetch('proses_penilaian.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('gradeModal'));
                        modal.hide();
                        showToast(data.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan', 'danger');
                });
            });
        });
    </script>
</body>
</html>