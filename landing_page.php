<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPiece - Portofolio PBL Polibatam</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background-color: whitesmoke;
        }

        .navbar {
            background: rgba(0, 0, 60, 0.8) !important;
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

        .hero {
            height: 100vh;
            background: url('bg-gedung.jpg') no-repeat center center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 30, 100, 0.5);
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-content span {
            color: #55bddd;
        }

        .hero-content p {
            font-size: 1.5rem;
        }

        .about {
            background: whitesmoke;
            color: #333;
            text-align: center;
            padding: 80px 0;
        }

        .about h2 {
            color: #003366;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .about p {
            max-width: 800px;
            margin: 0 auto 60px auto;
            font-size: 1.1rem;
        }

        .feature-box {
            background: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .feature-box i {
            color: #00bcd4;
            /* Warna ikon */
        }

        .feature-box h4 {
            color: #003366;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .feature-box p {
            color: #666;
            font-size: 1rem;
        }

        /* --- Responsif --- */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content p {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login_page.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1>Halo! Selamat datang di <span>WorkPiece</span></h1>
            <p>Temukan proyek yang menarik!</p>
        </div>
    </section>

    <!-- Tentang Section -->
    <section id="tentang" class="about">
        <div class="container">
            <h2>Tentang WorkPiece</h2>
            <p>Platform ini bertujuan untuk menjadi wadah utama bagi mahasiswa Polibatam dalam menampilkan karya dan
                proyek mereka, serta memberikan akses mudah bagi pengunjung untuk menjelajahi berbagai inovasi menarik.
            </p>

            <div class="row g-4"> <!-- Bootstrap Grid, g-4 untuk gutter/spasi -->
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="bi bi-lightbulb fs-1"></i>
                        <h4>Inovasi</h4>
                        <p>Wadah untuk menampilkan proyek inovatif mahasiswa Polibatam</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="bi bi-people fs-1"></i>
                        <h4>Kolaborasi</h4>
                        <p>Menghubungkan mahasiswa dengan potensi kolaborasi proyek</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="bi bi-trophy fs-1"></i>
                        <h4>Prestasi</h4>
                        <p>Menampilkan karya terbaik dan prestasi mahasiswa</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Kode JavaScript khusus Anda bisa ditulis di sini
            // Contoh: console.log("Halaman telah dimuat");
        });
    </script>
</body>

</html>