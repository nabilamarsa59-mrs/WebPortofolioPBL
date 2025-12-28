<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    header("location:../login.php");
    exit();
}

require_once '../koneksi.php';

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
           k.nama_kategori
        FROM projects p
        JOIN mahasiswa m ON p.id_mahasiswa = m.id
        LEFT JOIN penilaian pen ON p.id = pen.id_project
        LEFT JOIN kategori_proyek k ON p.kategori = k.id
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: whitesmoke;
            padding-top: 100px;
            margin: 0;
            padding-bottom: 0;
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

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 500;
            margin-left: 25px;
            transition: color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #00ffff !important;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .search-container {
            position: relative;
            margin-left: 25px;
        }

        .search-form {
            display: flex;
            align-items: center;
        }

        .search-form input {
            border: none;
            border-radius: 20px;
            padding: 5px 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            width: 200px;
            transition: width 0.3s, background-color 0.3s;
        }

        .search-form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-form input:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.3);
            width: 250px;
        }

        .search-form button {
            background: none;
            border: none;
            color: white;
            margin-left: -35px;
            cursor: pointer;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            margin-top: 5px;
        }

        .search-result-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            color: #333;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .highlight {
            background-color: #ffeb3b;
            padding: 0 2px;
        }

        .no-results {
            padding: 15px;
            text-align: center;
            color: #666;
        }

        .filter-section {
    background-color: rgba(0, 30, 100, 0.05);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.filter-section label.form-label {
    color: #00003c;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}

.filter-section .form-select {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.filter-section .form-select:focus {
    border-color: #00003c;
    box-shadow: 0 0 0 0.2rem rgba(0, 0, 60, 0.15);
}

        .project-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .penilaian-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .penilaian-section h6 {
            color: #00003c;
            border-bottom: 2px solid #00003c;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .nilai-badge {
            font-size: 1.2rem;
            padding: 8px 16px;
        }

        .text-navy {
            color: #00003c !important;
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

        .youtube-link-section {
            background-color: #fff3cd;
            border-left: 4px solid #ff0000;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .youtube-link-section i {
            color: #ff0000;
        }

        .footer-custom {
            background-color: #00003C;
            color: whitesmoke;
            padding: 20px 0;
            margin-top: auto;
            width: 100%;
        }

        /* Responsive Styles */
        @media (max-width: 991.98px) {
            body {
                padding-top: 80px;
            }

            .navbar-nav .nav-link {
                margin-left: 0;
                margin-bottom: 0.5rem;
            }

            .search-container {
                margin: 10px 0;
                width: 100%;
            }

            .search-form input {
                width: 100%;
            }

            .search-form input:focus {
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
    .filter-section {
        padding: 15px;
    }
    
    .filter-section label.form-label {
        font-size: 0.9rem;
    }
    
    .filter-section .form-select-sm {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        height: auto;
    }
    
    .filter-section .btn-sm {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        height: auto;
    }
    
    .filter-section .row.g-2 {
        row-gap: 0.75rem !important;
    }
}

@media (max-width: 575.98px) {
    .filter-section {
        padding: 12px;
    }
    
    .filter-section label.form-label {
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }
    
    .filter-section .form-select-sm {
        font-size: 0.813rem;
        padding: 0.45rem 0.65rem;
    }
    
    .filter-section .btn-sm {
        font-size: 0.813rem;
        padding: 0.45rem 0.85rem;
    }
}
    </style>
</head>

<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="profil_dosen.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link active" href="home_dosen.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    <li class="nav-item">
                        <div class="search-container">
                            <form class="search-form" role="search" id="searchForm">
                                <input type="search" id="searchInput" placeholder="Cari NIM atau Nama..."
                                    autocomplete="off">
                                <button type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                            <div class="search-results" id="searchResults"></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <h2 class="mt-4 mb-3 text-navy">Dashboard Penilaian Proyek Mahasiswa</h2>

        <div class="filter-section">
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label mb-2 fw-semibold">Filter:</label>
                </div>
                
                <!-- Status Filter -->
                <div class="col-12 col-md-6 col-lg-3">
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="all">Semua Status</option>
                        <option value="belum-dinilai">Belum Dinilai</option>
                        <option value="sudah-dinilai">Sudah Dinilai</option>
                    </select>
                </div>
                
                <!-- Jurusan Filter -->
                <div class="col-12 col-md-6 col-lg-3">
                    <select class="form-select form-select-sm" id="jurusanFilter">
                        <option value="all">Semua Jurusan</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Mesin">Teknik Mesin</option>
                        <option value="Manajemen dan Bisnis">Manajemen dan Bisnis</option>
                    </select>
                </div>
                
                <!-- Kategori Filter -->
                <div class="col-12 col-md-8 col-lg-4">
                    <select class="form-select form-select-sm" id="kategoriFilter">
                        <option value="all">Semua Kategori</option>
                        <option value="Aplikasi Web">Aplikasi Web</option>
                        <option value="Aplikasi Mobile">Aplikasi Mobile</option>
                        <option value="IoT">IoT</option>
                        <option value="Desain & Manufaktur">Desain & Manufaktur</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Game">Game</option>
                        <option value="Sistem Elektronika">Sistem Elektronika</option>
                        <option value="Robotika">Robotika</option>
                        <option value="Mekatronika">Mekatronika</option>
                        <option value="Manufaktur">Manufaktur</option>
                        <option value="Sistem Informasi Bisnis">Sistem Informasi Bisnis</option>
                        <option value="Logistik & Supply Chain">Logistik & Supply Chain</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="col-12 col-md-4 col-lg-2">
                    <button class="btn btn-sm btn-primary w-100" id="resetFilters">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
                
                <!-- Project Count -->
                <div class="col-12 mt-2">
                    <span class="text-muted small">
                        Menampilkan <span id="projectCount"><?= count($projects) ?></span> proyek
                    </span>
                </div>
            </div>
        </div>


        <div class="row" id="projectList">
            <?php if (empty($projects)): ?>
                <div class="col-12">
                    <p class="text-center mt-4">Belum ada proyek mahasiswa yang diajukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col-12 col-sm-6 col-lg-4 project-item"
                        data-status="<?= $project['nilai'] ? 'sudah-dinilai' : 'belum-dinilai' ?>"
                        data-jurusan="<?= htmlspecialchars($project['jurusan']) ?>"
                        data-kategori="<?= htmlspecialchars($project['nama_kategori'] ?? 'Lainnya') ?>"
                        data-nim="<?= htmlspecialchars($project['nim']) ?>"
                        data-student-name="<?= htmlspecialchars($project['nama_lengkap']) ?>">
                        <div class="card project-card h-100">
                            <img src="../uploads/<?= htmlspecialchars($project['gambar'] ?? 'default-project.png') ?>"
                                class="card-img-top" alt="Project">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($project['judul']) ?></h5>
                                <p class="card-text text-muted small">Oleh: <?= htmlspecialchars($project['nama_lengkap']) ?>
                                    (<?= htmlspecialchars($project['nim']) ?>)</p>
                                <p class="card-text small text-info mb-2">
                                    <i class="bi bi-building me-1"></i><?= htmlspecialchars($project['jurusan']) ?>
                                </p>
                                <p class="card-text small text-secondary mb-2">
                                    <i class="bi bi-tag me-1"></i><?= htmlspecialchars($project['nama_kategori'] ?? 'Lainnya') ?>
                                </p>
                                <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>
                                <div class="mt-auto">
                                    <?php if ($project['nilai']): ?>
                                        <span class="badge bg-success">Sudah Dinilai
                                            (<?= htmlspecialchars($project['nilai']) ?>)</span>
                                        <button class="btn btn-secondary btn-sm float-end" data-bs-toggle="modal"
                                            data-bs-target="#gradeModal" data-id="<?= $project['id'] ?>"
                                            data-title="<?= htmlspecialchars($project['judul']) ?>"
                                            data-student="<?= htmlspecialchars($project['nama_lengkap']) ?>"
                                            data-nim="<?= htmlspecialchars($project['nim']) ?>"
                                            data-jurusan="<?= htmlspecialchars($project['jurusan']) ?>"
                                            data-kategori="<?= htmlspecialchars($project['nama_kategori'] ?? 'Lainnya') ?>"
                                            data-description="<?= htmlspecialchars($project['deskripsi']) ?>"
                                            data-link-demo="<?= htmlspecialchars($project['link_demo'] ?? '') ?>"
                                            data-nilai="<?= htmlspecialchars($project['nilai']) ?>"
                                            data-komentar="<?= htmlspecialchars($project['komentar']) ?>"
                                            data-status="sudah-dinilai">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Belum Dinilai</span>
                                        <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal"
                                            data-bs-target="#gradeModal" data-id="<?= $project['id'] ?>"
                                            data-title="<?= htmlspecialchars($project['judul']) ?>"
                                            data-student="<?= htmlspecialchars($project['nama_lengkap']) ?>"
                                            data-nim="<?= htmlspecialchars($project['nim']) ?>"
                                            data-jurusan="<?= htmlspecialchars($project['jurusan']) ?>"
                                            data-kategori="<?= htmlspecialchars($project['nama_kategori'] ?? 'Lainnya') ?>"
                                            data-description="<?= htmlspecialchars($project['deskripsi']) ?>"
                                            data-link-demo="<?= htmlspecialchars($project['link_demo'] ?? '') ?>"
                                            data-status="belum-dinilai">
                                            <i class="bi bi-eye"></i> Nilai
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

    <!-- Modal -->
    <div class="modal fade" id="gradeModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProjectTitle">Judul Proyek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <p class="mb-1"><strong>Nama:</strong> <span id="modalStudentName">-</span></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <p class="mb-1"><strong>NIM:</strong> <span id="modalStudentNim">-</span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-2 mb-md-0">
                            <p class="mb-1"><strong>Jurusan:</strong> <span id="modalJurusan">-</span></p>
                        </div>
                        <div class="col-12 col-md-6">
                            <p class="mb-1"><strong>Kategori:</strong> <span id="modalProjectKategori">-</span></p>
                        </div>
                    </div>

                    <div id="youtubeLinkSection" class="youtube-link-section" style="display: none;">
                        <p class="mb-2"><strong><i class="bi bi-youtube me-2"></i>Video Proyek:</strong></p>
                        <a href="#" id="youtubeLink" target="_blank" class="btn btn-danger btn-sm">
                            <i class="bi bi-play-circle-fill me-1"></i>Tonton Video YouTube
                        </a>
                    </div>

                    <p><strong>Deskripsi:</strong></p>
                    <p id="modalProjectDescription">-</p>

                    <div id="existingGradeSection" class="penilaian-section" style="display: none;">
                        <h6><i class="bi bi-clipboard-check me-2"></i>Penilaian Anda</h6>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-2">
                                <p class="mb-2"><strong>Nilai:</strong></p>
                                <span id="existingNilai" class="badge bg-success nilai-badge">-</span>
                            </div>
                            <div class="col-12 col-md-8">
                                <p class="mb-2"><strong>Komentar:</strong></p>
                                <p id="existingKomentar" class="text-muted fst-italic">-</p>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6>Berikan Penilaian dan Komentar</h6>
                    <form id="gradeForm">
                        <div class="mb-3">
                            <label for="gradeSelect" class="form-label">Nilai</label>
                            <select class="form-select" id="gradeSelect">
                                <option value="" selected disabled>-- Pilih Nilai --</option>
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
                            <textarea class="form-control" id="commentText" rows="4"
                                placeholder="Berikan masukan..."></textarea>
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

    <div class="toast-container position-fixed top-0 end-0 p-3"></div>
    
    <!-- Footer -->
    <footer class="footer-custom">
        <div class="container">
            <p class="text-center mb-0">&copy; 2025 Politeknik Negeri Batam - Projek PBL IFPagi 1A-5</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function showToast(message, type = 'success') {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();
                const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';

                const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgColor} border-0" role="alert">
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

                toastElement.addEventListener('hidden.bs.toast', () => toastElement.remove());
            }

            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            function updateProjectCount() {
                const visibleProjects = document.querySelectorAll('.project-item:not(.d-none)').length;
                document.getElementById('projectCount').textContent = visibleProjects;
            }

            function applyFilters() {
                const statusFilter = document.getElementById('statusFilter').value;
                const jurusanFilter = document.getElementById('jurusanFilter').value;
                const kategoriFilter = document.getElementById('kategoriFilter').value;

                const projectItems = document.querySelectorAll('.project-item');

                projectItems.forEach(item => {
                    const status = item.dataset.status;
                    const jurusan = item.dataset.jurusan;
                    const kategori = item.dataset.kategori;

                    let showItem = true;

                    // Filter status
                    if (statusFilter !== 'all' && status !== statusFilter) {
                        showItem = false;
                    }

                    // Filter jurusan - PERBAIKAN: Perbandingan exact match
                    if (jurusanFilter !== 'all' && jurusan !== jurusanFilter) {
                        showItem = false;
                    }

                    // Filter kategori
                    if (kategoriFilter !== 'all' && kategori !== kategoriFilter) {
                        showItem = false;
                    }

                    if (showItem) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });

                updateProjectCount();
            }

            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('jurusanFilter').addEventListener('change', applyFilters);
            document.getElementById('kategoriFilter').addEventListener('change', applyFilters);

            document.getElementById('resetFilters').addEventListener('click', function () {
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('jurusanFilter').value = 'all';
                document.getElementById('kategoriFilter').value = 'all';

                applyFilters();
                showToast('Filter telah direset');
            });

            function searchProjects(query) {
                const projectItems = document.querySelectorAll('.project-item');
                const searchResults = document.getElementById('searchResults');

                if (query.trim() === '') {
                    searchResults.style.display = 'none';
                    projectItems.forEach(item => {
                        item.classList.remove('d-none');
                    });
                    updateProjectCount();
                    return;
                }

                searchResults.innerHTML = '';
                let hasResults = false;

                projectItems.forEach(item => {
                    const nim = item.dataset.nim;
                    const studentName = item.dataset.studentName.toLowerCase();
                    const title = item.querySelector('.card-title').textContent.toLowerCase();

                    if (nim.includes(query) || studentName.includes(query.toLowerCase()) || title.includes(query.toLowerCase())) {
                        item.classList.remove('d-none');

                        const resultItem = document.createElement('div');
                        resultItem.className = 'search-result-item';

                        let highlightedName = item.dataset.studentName;
                        let highlightedNim = nim;

                        if (studentName.includes(query.toLowerCase())) {
                            const regex = new RegExp(`(${query})`, 'gi');
                            highlightedName = highlightedName.replace(regex, '<span class="highlight">$1</span>');
                        }

                        if (nim.includes(query)) {
                            const regex = new RegExp(`(${query})`, 'gi');
                            highlightedNim = highlightedNim.replace(regex, '<span class="highlight">$1</span>');
                        }

                        resultItem.innerHTML = `
                    <div><strong>${highlightedName}</strong> (${highlightedNim})</div>
                    <div class="small text-muted">${item.querySelector('.card-title').textContent}</div>
                `;

                        resultItem.addEventListener('click', function () {
                            item.scrollIntoView({ behavior: 'smooth', block: 'center' });

                            const card = item.querySelector('.project-card');
                            card.style.transition = 'box-shadow 0.3s';
                            card.style.boxShadow = '0 0 15px rgba(0, 51, 102, 0.5)';

                            setTimeout(() => {
                                card.style.boxShadow = '';
                            }, 2000);

                            searchResults.style.display = 'none';
                        });

                        searchResults.appendChild(resultItem);
                        hasResults = true;
                    } else {
                        item.classList.add('d-none');
                    }
                });

                if (hasResults) {
                    searchResults.style.display = 'block';
                } else {
                    searchResults.innerHTML = '<div class="no-results">Tidak ada hasil yang ditemukan</div>';
                    searchResults.style.display = 'block';
                }

                updateProjectCount();
            }

            document.getElementById('searchForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const query = document.getElementById('searchInput').value;
                searchProjects(query);
            });

            document.getElementById('searchInput').addEventListener('input', function () {
                const query = this.value;
                searchProjects(query);
            });

            document.addEventListener('click', function (e) {
                const searchContainer = document.querySelector('.search-container');
                if (!searchContainer.contains(e.target)) {
                    document.getElementById('searchResults').style.display = 'none';
                }
            });

            document.getElementById('gradeModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-id');
                const title = button.getAttribute('data-title');
                const student = button.getAttribute('data-student');
                const nim = button.getAttribute('data-nim');
                const jurusan = button.getAttribute('data-jurusan');
                const kategori = button.getAttribute('data-kategori');
                const description = button.getAttribute('data-description');
                const linkDemo = button.getAttribute('data-link-demo');
                const status = button.getAttribute('data-status');
                const nilai = button.getAttribute('data-nilai');
                const komentar = button.getAttribute('data-komentar');

                document.getElementById('gradeModal').setAttribute('data-project-id', id);

                document.getElementById('modalProjectTitle').textContent = title;
                document.getElementById('modalStudentName').textContent = student;
                document.getElementById('modalStudentNim').textContent = nim;
                document.getElementById('modalJurusan').textContent = jurusan;
                document.getElementById('modalProjectKategori').textContent = kategori;
                document.getElementById('modalProjectDescription').textContent = description;

                const youtubeLinkSection = document.getElementById('youtubeLinkSection');
                const youtubeLink = document.getElementById('youtubeLink');

                if (linkDemo && linkDemo.trim() !== '') {
                    youtubeLink.href = linkDemo;
                    youtubeLinkSection.style.display = 'block';
                } else {
                    youtubeLinkSection.style.display = 'none';
                }

                const existingSection = document.getElementById('existingGradeSection');

                if (status === 'sudah-dinilai' && nilai) {
                    document.getElementById('existingNilai').textContent = nilai;
                    document.getElementById('existingKomentar').textContent = komentar || '(Tidak ada komentar)';
                    existingSection.style.display = 'block';

                    document.getElementById('gradeSelect').value = nilai;
                    document.getElementById('commentText').value = komentar || '';

                    document.getElementById('saveGradeBtn').innerHTML = '<i class="bi bi-check-circle"></i> Perbarui Penilaian';
                } else {
                    existingSection.style.display = 'none';
                    document.getElementById('gradeForm').reset();
                    document.getElementById('saveGradeBtn').innerHTML = '<i class="bi bi-check-circle"></i> Simpan Penilaian';
                }
            });

            document.getElementById('gradeModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('gradeForm').reset();
                document.getElementById('existingGradeSection').style.display = 'none';
                document.getElementById('youtubeLinkSection').style.display = 'none';
            });

            document.getElementById('saveGradeBtn').addEventListener('click', function () {
                const grade = document.getElementById('gradeSelect').value;
                const comment = document.getElementById('commentText').value;
                const projectId = document.getElementById('gradeModal').getAttribute('data-project-id');

                if (!grade) {
                    showToast('Silakan pilih nilai terlebih dahulu', 'danger');
                    return;
                }

                if (!projectId) {
                    showToast('Terjadi kesalahan: ID Proyek tidak ditemukan', 'danger');
                    return;
                }

                showLoading();

                fetch('proses_penilaian.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'id_project': projectId,
                        'nilai': grade,
                        'komentar': comment
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();

                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('gradeModal'));
                            modal.hide();

                            showToast(data.message, 'success');

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showToast(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat menyimpan penilaian', 'danger');
                    });
            });
        });
    </script>
</body>

</html>