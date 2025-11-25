<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPiece - Home</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: whitesmoke;
        }
        .hero {
            height: 100vh;
            background: url('bg-gedung.jpg') center/cover no-repeat;
            position: relative;
        }
        .hero::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 30, 100, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            top: 45%;
            transform: translateY(-50%);
            text-align: center;
            color: white;
        }
        nav a:hover { color: #00ffff !important; }
        .dropdown-menu { background-color: rgba(0, 0, 60, 0.9); }
        .dropdown-menu a { color: white; }
        .dropdown-menu a:hover { background-color: rgba(0, 255, 255, 0.2); }
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
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#tentang">Tentang</a>
                    </li>

                    <!-- Dropdown Profil -->
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Profil
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profil_page.php">Profil</a></li>
                            <li><a class="dropdown-item" href="landing_page.php">Logout</a></li>
                        </ul>
                    </li>

                    <!-- Search -->
                    <li class="nav-item ms-3">
                        <form class="d-flex" role="search">
                            <input class="form-control form-control-sm me-2" type="search" placeholder="Cari proyek...">
                            <button class="btn btn-outline-light btn-sm" type="submit">🔍</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero d-flex align-items-center justify-content-center">
        <div class="hero-content">
            <h1 class="fw-bold">Halo! Selamat datang di WEB Portofolio PBL<span style="color:#55bddd; text-decoration:underline">WorkPiece</span></h1>
            <p class="mt-2">Temukan proyek yang menarik!</p>
        </div>
    </section>

    <!-- Tentang -->
    <section id="tentang" class="py-5 text-center" style="background: whitesmoke; color:#333;">
        <div class="container px-5">
            <h2 class="mb-3" style="color:#003366">Tentang</h2>
            <p>Platform ini bertujuan menjadi wadah utama bagi mahasiswa Polibatam dalam menampilkan karya dan proyek mereka, serta memberi akses mudah bagi pengunjung untuk menjelajahi berbagai inovasi menarik.</p>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
