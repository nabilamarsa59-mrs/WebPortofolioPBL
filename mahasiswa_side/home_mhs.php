<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoliKarya - Portofolio PBL Polibatam</title>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand ms-1" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Dropdown Profil -->
            <li class="dropdown dropdown-profil">
                <a href="#" class="dropbtn">Profil ▾</a>
                <div class="dropdown-content">
                    <a href="profil_page.php">Profil</a>
                    <a href="upload_project.php">Proyek</a>
                    <a href="landing_page.php">Logout</a>
                </div>
            </li>
            <!-- Search Bar (setelah profil) -->
            <li class="search-container">
                <form action="#" class="search-form">
                    <input type="text" placeholder="Cari proyek atau nama..." class="search-input">
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </li>
            </ul>
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
        <h2>Tentang</h2>
        <p>Platform ini bertujuan untuk menjadi wadah utama bagi mahasiswa Polibatam dalam menampilkan karya dan proyek
            mereka, serta memberikan akses mudah bagi pengunjung untuk menjelajahi berbagai inovasi menarik.</p>
    </section>
    <script src="script_home.js"></script>
</body>

</html>