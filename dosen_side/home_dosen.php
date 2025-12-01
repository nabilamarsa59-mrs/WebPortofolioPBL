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
            background-color: whitesmoke;
        }

        nav a:hover {
            color: #00ffff !important;
        }

        .dropdown-menu {
            background-color: rgba(0, 0, 60, 0.9);
        }

        .dropdown-menu a {
            color: white;
        }

        .dropdown-menu a:hover {
            background-color: rgba(0, 255, 255, 0.2);
        }

        .content-wrapper {
            padding-top: 80px;
            min-height: 100vh;
        }

        .project-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            margin-bottom: 20px;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
        }

        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: rgba(0,0,60,0.85)">
        <div class="container-fluid px-5">
            <a class="navbar-brand fw-bold" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item mx-2"><a class="nav-link" href="home_page.php">Beranda</a></li>
                    <li class="nav-item mx-2"><a class="nav-link active" href="dashboard_dosen.php">Dashboard</a></li>
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Profil</a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profil_page.php">Profil Saya</a></li>
                            <li><a class="dropdown-item" href="landing_page.php">Logout</a></li>
                        </ul>
                    </li>
                    <li class="nav-item ms-3">
                        <form class="d-flex" role="search">
                            <input class="form-control form-control-sm me-2" type="search" id="searchInput"
                                placeholder="Cari NIM atau Nama...">
                            <button class="btn btn-outline-light btn-sm" type="submit">🔍︎</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container px-5">
            <div class="row">
                <div class="col-12">
                    <h2 class="mt-4 mb-3" style="color:#003366">Dashboard Penilaian Proyek Mahasiswa</h2>

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
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Teknik Elektro">Teknik Elektro</option>
                                        <option value="Teknik Mesin">Teknik Mesin</option>
                                        <option value="Manajemen dan Bisnis">Manajemen dan Bisnis</option>
                                    </select>
                                    <select class="form-select form-select-sm" id="prodiFilter" style="width: auto;"
                                        disabled>
                                        <option value="all">Pilih Jurusan Dulu</option>
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
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted">Menampilkan <span id="projectCount">6</span> proyek</span>
                            </div>
                        </div>
                    </div>

                    <!-- Project Cards -->
                    <div class="row" id="projectList">
                        <!-- Project 1 -->
                        <div class="col-md-6 col-lg-4 project-item" data-status="belum-dinilai"
                            data-jurusan="Teknik Informatika" data-prodi="D4 Rekayasa Perangkat Lunak"
                            data-kategori="Aplikasi Web" data-nim="4342101234" data-student-name="Budi Santoso">
                            <div class="card project-card h-100">
                                <img src="https://picsum.photos/seed/project1/400/200.jpg" class="card-img-top"
                                    alt="Project Image">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">Aplikasi E-Commerce Berbasis Web</h5>
                                    <p class="card-text text-muted small">Oleh: Budi Santoso (4342101234)</p>
                                    <p class="card-text small text-info mb-2">
                                        <i class="bi bi-building me-1"></i><span class="card-jurusan">Teknik
                                            Informatika</span> - <span class="card-prodi">D4 Rekayasa Perangkat
                                            Lunak</span>
                                    </p>
                                    <p class="card-text">Pengembangan aplikasi penjualan online dengan fitur keranjang
                                        belanja dan pembayaran.</p>
                                    <div class="mt-auto">
                                        <span class="badge bg-warning status-badge">Belum Dinilai</span>
                                        <button class="btn btn-primary btn-sm float-end" data-bs-toggle="modal"
                                            data-bs-target="#gradeModal" data-id="1"
                                            data-title="Aplikasi E-Commerce Berbasis Web" data-student="Budi Santoso"
                                            data-nim="4342101234" data-jurusan="Teknik Informatika"
                                            data-prodi="D4 Rekayasa Perangkat Lunak" data-kategori="Aplikasi Web"
                                            data-description="Pengembangan aplikasi penjualan online dengan fitur keranjang belanja dan pembayaran.">
                                            <i class="bi bi-eye"></i> Lihat & Nilai
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project 2 -->
                        <div class="col-md-6 col-lg-4 project-item" data-status="sudah-dinilai"
                            data-jurusan="Teknik Elektro" data-prodi="D4 Teknologi Rekayasa Internet of Things (IoT)"
                            data-kategori="IoT" data-nim="4342105678" data-student-name="Siti Aminah">
                            <div class="card project-card h-100">
                                <img src="https://picsum.photos/seed/project2/400/200.jpg" class="card-img-top"
                                    alt="Project Image">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">Sistem Monitoring IoT</h5>
                                    <p class="card-text text-muted small">Oleh: Siti Aminah (4342105678)</p>
                                    <i class="bi bi-building me-1"></i><span class="card-jurusan">Teknik
                                        Informatika</span> - <span class="card-prodi">D4 Rekayasa Perangkat Lunak</span>
                                    </p>
                                    <p class="card-text">Sistem untuk memantau suhu dan kelembaban ruangan secara
                                        real-time menggunakan teknologi IoT.</p>
                                    <div class="mt-auto">
                                        <span class="badge bg-success status-badge">Sudah Dinilai (A)</span>
                                        <button class="btn btn-secondary btn-sm float-end" data-bs-toggle="modal"
                                            data-bs-target="#gradeModal" data-id="2" data-title="Sistem Monitoring IoT"
                                            data-student="Siti Aminah" data-nim="4342105678"
                                            data-jurusan="Teknik Elektro"
                                            data-prodi="D4 Teknologi Rekayasa Internet of Things (IoT)"
                                            data-kategori="IoT"
                                            data-description="Sistem untuk memantau suhu dan kelembaban ruangan secara real-time menggunakan teknologi IoT.">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tambahkan lebih banyak kartu proyek dengan data lengkap di sini -->
                        <!-- Project 3 -->
                        <div class="col-md-6 col-lg-4 project-item" data-status="belum-dinilai"
                            data-jurusan="Teknik Mesin" data-prodi="D4 Teknologi Rekayasa Manufaktur"
                            data-kategori="Desain & Manufaktur" data-nim="4342109876" data-student-name="Ahmad Fauzi">
                            <div class="card project-card h-100"> <!-- ... isinya serupa ... --> </div>
                        </div>
                        <!-- Project 4 -->
                        <div class="col-md-6 col-lg-4 project-item" data-status="sudah-dinilai"
                            data-jurusan="Manajemen dan Bisnis" data-prodi="D4 Logistik Bisnis"
                            data-kategori="Sistem Informasi" data-nim="4342105432" data-student-name="Rina Wijaya">
                            <div class="card project-card h-100"> <!-- ... isinya serupa ... --> </div>
                        </div>
                        <!-- Project 5 -->
                        <div class="col-md-6 col-lg-4 project-item" data-status="belum-dinilai"
                            data-jurusan="Teknik Informatika" data-prodi="D4 Teknologi Rekayasa Multimedia"
                            data-kategori="Game" data-nim="4342108765" data-student-name="Dedi Prasetyo">
                            <div class="card project-card h-100"> <!-- ... isinya serupa ... --> </div>
                        </div>
                        <!-- Project 6 -->
                        <div class="col-md-6 col-lg-4 project-item" data-status="belum-dinilai"
                            data-jurusan="Teknik Elektro" data-prodi="D4 Teknik Elektronika" data-kategori="IoT"
                            data-nim="4342102345" data-student-name="Eko Susilo">
                            <div class="card project-card h-100"> <!-- ... isinya serupa ... --> </div>
                        </div>
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
                        <div class="col-md-6">
                            <p><strong>Jurusan:</strong> <span id="modalStudentJurusan">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Prodi:</strong> <span id="modalStudentProdi">-</span></p>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script_dosen.js"></script>
</body>

</html>