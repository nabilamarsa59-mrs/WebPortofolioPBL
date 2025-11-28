<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoliKarya - Portofolio PBL Polibatam</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="RegisterPage.php">Daftar Akun</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="LoginPage.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="overlay"></div>
        <div class="container">
            <div class="hero-content">
                <h1>Halo! Selamat datang di <span>WorkPiece</span></h1>
                <p>Temukan proyek yang menarik!</p>
                <a href="#tentang" class="btn btn-primary btn-lg">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </section>

    <!-- Tentang Section -->
    <section id="tentang" class="about py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="mb-4">Tentang WorkPiece</h2>
                    <p class="lead">Platform ini bertujuan untuk menjadi wadah utama bagi mahasiswa Polibatam dalam menampilkan karya dan proyek mereka, serta memberikan akses mudah bagi pengunjung untuk menjelajahi berbagai inovasi menarik.</p>
                    <div class="row mt-5">
                        <div class="col-md-4 mb-4">
                            <div class="feature-box">
                                <i class="bi bi-lightbulb fs-1 text-primary mb-3"></i>
                                <h4>Inovasi</h4>
                                <p>Wadah untuk menampilkan proyek inovatif mahasiswa Polibatam</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="feature-box">
                                <i class="bi bi-people fs-1 text-primary mb-3"></i>
                                <h4>Kolaborasi</h4>
                                <p>Menghubungkan mahasiswa dengan potensi kolaborasi proyek</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="feature-box">
                                <i class="bi bi-trophy fs-1 text-primary mb-3"></i>
                                <h4>Prestasi</h4>
                                <p>Menampilkan karya terbaik dan prestasi mahasiswa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>WorkPiece</h5>
                    <p>Platform Portofolio PBL Polibatam</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2023 WorkPiece. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="script_landing.js"></script>
</body>
</html>