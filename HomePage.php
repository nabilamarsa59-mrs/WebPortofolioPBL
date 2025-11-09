<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PoliKarya - Portofolio PBL Polibatam</title>
    <link rel="stylesheet" href="styleHome.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">WorkPiece</div>
        <ul class="nav-links">
    <li><a href="#beranda">Beranda</a></li>
    <li><a href="#tentang">Tentang</a></li>

    <!-- Dropdown Profil -->
    <li class="dropdown dropdown-profil">
        <a href="#" class="dropbtn">Profil ▾</a>
        <div class="dropdown-content">
            <a href="ProfilPage.php">Profil</a>
            <a href="UploadProject.php">Proyek</a>
            <a href="LandingPage.php">Logout</a>
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
        <p>Platform ini bertujuan untuk menjadi wadah utama bagi mahasiswa Polibatam dalam menampilkan karya dan proyek mereka, serta memberikan akses mudah bagi pengunjung untuk menjelajahi berbagai inovasi menarik.</p>
    </section>
    <script src="scriptHome.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
