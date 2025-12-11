<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    header("location:../login.php");
    exit();
}

require_once '../koneksi.php'; // Sambungkan ke database

// --- KODE UNTUK MENGAMBIL DATA ---
// Query untuk mengambil semua data proyek beserta data mahasiswa dan penilaiannya
// Menggunakan LEFT JOIN karena proyek mungkin belum dinilai
 $sql = "SELECT
           p.id,
           p.judul,
           p.deskripsi,
           p.kategori,
           p.tanggal,
           p.gambar,
           p.link_demo,
           m.nama_lengkap,      -- DIAMBIL DARI TABEL MAHASISWA
           m.nim,
           m.jurusan,           -- DIAMBIL DARI TABEL MAHASISWA
           pen.nilai,
           pen.komentar
        FROM projects p
        JOIN mahasiswa m ON p.id_mahasiswa = m.id
        LEFT JOIN penilaian pen ON p.id = pen.id_project
        ORDER BY p.tanggal DESC";

 $stmt = $pdo->prepare($sql);
 $stmt->execute();
 $projects = $stmt->fetchAll(); // Simpan semua hasil ke dalam array $projects
// --- AKHIR KODE PENGGAMBIL DATA ---
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPiece - Dashboard Dosen</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: whitesmoke;
            padding-top: 100px;
        }

        .navbar {
            background: rgba(0, 0, 60, 0.8) !important;
            padding: 0.75rem 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
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

        .navbar a:hover {
            text-decoration: underline;
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

        .content-wrapper {
            min-height: 100vh;
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

        .filter-section {
            background-color: rgba(0, 30, 100, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            font-size: 0.8rem;
        }

        .text-navy {
            color: #00003c !important;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0, 0, 0, .1);
            border-radius: 50%;
            border-top-color: #003366;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .filter-active {
            background-color: #003366;
            color: white;
        }

        @media (max-width: 768px) {
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

            .filter-section .d-flex {
                flex-direction: column;
            }

            .filter-section .form-select {
                width: 100% !important;
                margin-bottom: 10px;
            }
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .project-item {
            animation: fadeIn 0.5s ease-out;
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
            <a class="navbar-brand" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                                    <select class="form-select form-select-sm" id="jurusanProdiFilter"
                                        style="width: auto;">
                                        <option value="all">Semua Jurusan dan Prodi</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Teknik Elektro">Teknik Elektro</option>
                                        <option value="Teknik Mesin">Teknik Mesin</option>
                                        <option value="Manajemen dan Bisnis">Manajemen dan Bisnis</option>
                                    </select>
                                    <select class="form-select form-select-sm" id="kategoriFilter" style="width: auto;">
                                        <option value="all">Semua Kategori</option>
                                        <option value="Aplikasi Web">Aplikasi Web</option>
                                        <option value="Aplikasi Mobile">Aplikasi Mobile</option>
                                        <option value="IoT">IoT</option>
                                        <option value="Desain & Manufaktur">Desain & Manufaktur</option>
                                        <option value="Sistem Informasi">Sistem Informasi</option>
                                        <option value="Game">Game</option>
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
                                     data-jurusan-prodi="<?= htmlspecialchars($project['jurusan']) ?>"
                                     data-kategori="<?= htmlspecialchars($project['kategori']) ?>"
                                     data-nim="<?= htmlspecialchars($project['nim']) ?>"
                                     data-student-name="<?= htmlspecialchars($project['nama_lengkap']) ?>">
                                    <div class="card project-card h-100">
                                        <img src="../uploads/<?= htmlspecialchars($project['gambar'] ?? 'default-project.png') ?>" class="card-img-top" alt="Project Image">
                                        <div class="card-body d-flex flex-column">
                                            <!-- PERHATIKAN PENGGUNAAN htmlspecialchars() DI BAWAH INI -->
                                            <h5 class="card-title"><?= htmlspecialchars($project['judul']) ?></h5>
                                            <p class="card-text text-muted small">Oleh: <?= htmlspecialchars($project['nama_lengkap']) ?> (<?= htmlspecialchars($project['nim']) ?>)</p>
                                            <p class="card-text small text-info mb-2">
                                                <i class="bi bi-building me-1"></i><span class="card-jurusan-prodi"><?= htmlspecialchars($project['jurusan']) ?></span>
                                            </p>
                                            <p class="card-text"><?= substr(htmlspecialchars($project['deskripsi']), 0, 80) . '...'; ?></p>
                                            <div class="mt-auto">
                                                <?php if ($project['nilai']): ?>
                                                    <span class="badge bg-success status-badge">Sudah Dinilai (<?= htmlspecialchars($project['nilai']) ?>)</span>
                                                    <button class="btn btn-secondary btn-sm float-end" data-bs-toggle="modal"
                                                        data-bs-target="#gradeModal" data-id="<?= $project['id'] ?>"
                                                        data-title="<?= htmlspecialchars($project['judul']) ?>" data-student="<?= htmlspecialchars($project['nama_lengkap']) ?>"
                                                        data-nim="<?= htmlspecialchars($project['nim']) ?>" data-jurusan="<?= htmlspecialchars($project['jurusan']) ?>"
                                                        data-kategori="<?= htmlspecialchars($project['kategori']) ?>"
                                                        data-description="<?= htmlspecialchars($project['deskripsi']) ?>"
                                                        data-status="sudah-dinilai">
                                                        <i class="bi bi-eye"></i> Lihat Detail
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-warning status-badge">Belum Dinilai</span>
                                                    <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal"
                                                        data-bs-target="#gradeModal" data-id="<?= $project['id'] ?>"
                                                        data-title="<?= htmlspecialchars($project['judul']) ?>" data-student="<?= htmlspecialchars($project['nama_lengkap']) ?>"
                                                        data-nim="<?= htmlspecialchars($project['nim']) ?>" data-jurusan="<?= htmlspecialchars($project['jurusan']) ?>"
                                                        data-kategori="<?= htmlspecialchars($project['kategori']) ?>"
                                                        data-description="<?= htmlspecialchars($project['deskripsi']) ?>"
                                                        data-status="belum-dinilai">
                                                        <i class="bi bi-eye"></i> Lihat & Nilai
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

    <!-- Modal untuk Penilaian  -->
    <div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProjectTitle">Judul Proyek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Nama Mahasiswa:</strong> <span id="modalStudentName">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>NIM:</strong> <span id="modalStudentNim">-</span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p><strong>Jurusan:</strong> <span id="modalJurusanProdi">-</span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Kategori:</strong> <span id="modalProjectKategori">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span id="modalProjectStatus" class="badge">-</span></p>
                        </div>
                    </div>
                    <p><strong>Deskripsi Proyek:</strong></p>
                    <p id="modalProjectDescription">-</p>
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
                    <button type="button" class="btn btn-success" id="saveGradeBtn"><i class="bi bi-check-circle"></i>
                        Simpan Penilaian</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi untuk menampilkan toast notifikasi
            function showToast(message, type = 'success') {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();

                const toastHtml = `
                    <div id="${toastId}" class="toast custom-toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
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

                // Hapus elemen toast setelah disembunyikan
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            }

            // Fungsi untuk menampilkan loading overlay
            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            // Fungsi untuk menyembunyikan loading overlay
            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            // Fungsi untuk memperbarui jumlah proyek yang ditampilkan
            function updateProjectCount() {
                const visibleProjects = document.querySelectorAll('.project-item:not(.d-none)').length;
                document.getElementById('projectCount').textContent = visibleProjects;
            }

            // Fungsi untuk menerapkan filter
            function applyFilters() {
                const statusFilter = document.getElementById('statusFilter').value;
                const jurusanProdiFilter = document.getElementById('jurusanProdiFilter').value;
                const kategoriFilter = document.getElementById('kategoriFilter').value;

                const projectItems = document.querySelectorAll('.project-item');

                projectItems.forEach(item => {
                    const status = item.dataset.status;
                    const jurusanProdi = item.dataset.jurusanProdi;
                    const kategori = item.dataset.kategori;

                    let showItem = true;

                    if (statusFilter !== 'all' && status !== statusFilter) {
                        showItem = false;
                    }

                    if (jurusanProdiFilter !== 'all' && jurusanProdi !== jurusanProdiFilter) {
                        showItem = false;
                    }

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

            // Event listener untuk perubahan filter
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('jurusanProdiFilter').addEventListener('change', applyFilters);
            document.getElementById('kategoriFilter').addEventListener('change', applyFilters);

            // Event listener untuk reset filter
            document.getElementById('resetFilters').addEventListener('click', function () {
                document.getElementById('statusFilter').value = 'all';
                document.getElementById('jurusanProdiFilter').value = 'all';
                document.getElementById('kategoriFilter').value = 'all';

                applyFilters();
                showToast('Filter telah direset');
            });

            // Fungsi untuk pencarian proyek
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

                        // Add to search results
                        const resultItem = document.createElement('div');
                        resultItem.className = 'search-result-item';

                        // Highlight matching text
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
                            // Scroll to the project card
                            item.scrollIntoView({ behavior: 'smooth', block: 'center' });

                            // Highlight the card temporarily
                            const card = item.querySelector('.project-card');
                            card.style.transition = 'box-shadow 0.3s';
                            card.style.boxShadow = '0 0 15px rgba(0, 51, 102, 0.5)';

                            setTimeout(() => {
                                card.style.boxShadow = '';
                            }, 2000);

                            // Hide search results
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

            // Event listener untuk form pencarian
            document.getElementById('searchForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const query = document.getElementById('searchInput').value;
                searchProjects(query);
            });

            // Event listener untuk input pencarian (real-time search)
            document.getElementById('searchInput').addEventListener('input', function () {
                const query = this.value;
                searchProjects(query);
            });

            // Sembunyikan hasil pencarian saat klik di luar
            document.addEventListener('click', function (e) {
                const searchContainer = document.querySelector('.search-container');
                if (!searchContainer.contains(e.target)) {
                    document.getElementById('searchResults').style.display = 'none';
                }
            });

            // Event listener untuk modal penilaian
            document.getElementById('gradeModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                // Ambil data dari atribut button
                const id = button.getAttribute('data-id');
                const title = button.getAttribute('data-title');
                const student = button.getAttribute('data-student');
                const nim = button.getAttribute('data-nim');
                const jurusan = button.getAttribute('data-jurusan');
                const kategori = button.getAttribute('data-kategori');
                const description = button.getAttribute('data-description');
                const status = button.getAttribute('data-status');

                // Simpan id_project ke atribut data modal
                document.getElementById('gradeModal').setAttribute('data-project-id', id);

                // Isi modal dengan data
                document.getElementById('modalProjectTitle').textContent = title;
                document.getElementById('modalStudentName').textContent = student;
                document.getElementById('modalStudentNim').textContent = nim;
                document.getElementById('modalJurusanProdi').textContent = jurusan;
                document.getElementById('modalProjectKategori').textContent = kategori;
                document.getElementById('modalProjectDescription').textContent = description;

                // Set status badge
                const statusBadge = document.getElementById('modalProjectStatus');
                statusBadge.className = 'badge';

                if (status === 'sudah-dinilai') {
                    statusBadge.classList.add('bg-success');
                    statusBadge.textContent = 'Sudah Dinilai';
                } else {
                    statusBadge.classList.add('bg-warning');
                    statusBadge.textContent = 'Belum Dinilai';
                }

                // Reset form
                document.getElementById('gradeForm').reset();
            });

            // Event listener untuk tombol simpan penilaian
            document.getElementById('saveGradeBtn').addEventListener('click', function () {
                const grade = document.getElementById('gradeSelect').value;
                const comment = document.getElementById('commentText').value;

                // Ambil id_project dari atribut data pada modal
                const projectId = document.getElementById('gradeModal').getAttribute('data-project-id');

                if (!grade) {
                    showToast('Silakan pilih nilai terlebih dahulu', 'danger');
                    return;
                }

                if (!projectId) {
                    showToast('Terjadi kesalahan: ID Proyek tidak ditemukan.', 'danger');
                    return;
                }

                // Tampilkan loading saat proses pengiriman data
                showLoading();

                // Gunakan fetch untuk mengirim data ke proses_penilaian.php
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
                        hideLoading(); // Sembunyikan loading setelah mendapat respons

                        if (data.success) {
                            // Tutup modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('gradeModal'));
                            modal.hide();

                            // Tampilkan notifikasi sukses
                            showToast(data.message, 'success');

                            // Reload halaman setelah 1.5 detik untuk melihat perubahan
                            setTimeout(() => {
                                location.reload();
                            }, 1500);

                        } else {
                            // Tampilkan notifikasi error dari server
                            showToast(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        hideLoading(); // Sembunyikan loading jika terjadi error
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat menyimpan penilaian', 'danger');
                    });
            });
        });
    </script>
</body>

</html>