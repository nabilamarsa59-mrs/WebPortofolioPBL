<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PBL Polibatam - Profil</title>

  <!-- Google Font Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Link Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Poppins', sans-serif;
      color: #333; /* Ubah warna teks default menjadi gelap untuk kontras */
      background-color: whitesmoke;
    }

    /* --- Navbar --- */
    .navbar {
      background-color: #002b5b !important;
      /* Padding dikurangi dari 1rem menjadi 0.75rem agar tidak terlalu tinggi */
      padding: 0.75rem 0;
      z-index: 1000;
    }

    /* Font size hanya untuk brand (logo) */
    .navbar-brand.ms-1 {
      font-size: 1.5rem;
      font-weight: bold;
      color: #fff !important;
    }

    /* Font size untuk link navigasi lainnya */
    .navbar .nav-link {
      margin: 0 12px;
      font-size: 16px;
      font-weight: normal; /* Memastikan tidak tebal */
    }

    /* Tambahkan style untuk gambar profil default */
    #previewFoto {
      background-color: #e9ecef;
    }
    
    /* TAMBAHKAN CSS INI */
    /* Jarak untuk konten agar tidak tertutup navbar */
    main {
        padding-top: 80px; /* Anda bisa menyesuaikan nilai ini */
    }

  </style>
</head>

<body class="bg-light">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand ms-1" href="#">WorkPiece</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="d-flex ms-auto">
        <a href="home_mhs.php" class="nav-link text-white">Beranda</a>
        <!-- Hapus class 'fw-bold' agar tidak tebal -->
        <a href="#" class="nav-link text-white text-decoration-underline">Profil</a>
        <a href="../landing_page.php">Logout</a>      
      </div>
    </div>
  </nav>

  <!-- Main Container -->
  <main class="container my-5">
    <div class="row g-4">
      <!-- KIRI -->
      <section class="col-md-4">
        <div class="card text-center shadow-sm p-4">
          <div class="mb-3">
            <img id="previewFoto" src="https://i.pinimg.com/736x/1b/62/b5/1b62b54b4bc754fa4a2980f77bd15a6b.jpg"
              alt="Foto Profil" class="rounded-circle border mx-auto d-block" width="120" height="120"
              style="object-fit: cover;">
          </div>

          <div class="d-grid gap-2">
            <input type="file" id="uploadFoto" accept="image/*" hidden>
            <button id="btnUnggah" class="btn btn-primary">Unggah Foto</button>
            <button id="btnHapus" class="btn btn-danger">Hapus Foto</button>
          </div>

          <hr>

          <div class="d-grid gap-2">
            <button id="editProfil" class="btn btn-warning text-white">Edit Profil</button>
            <button id="simpanPerubahan" class="btn btn-success">Simpan</button>
          </div>
        </div>
      </section>

      <!-- KANAN -->
      <section class="col-md-8">
        <!-- FORM PROFIL -->
        <div class="card shadow-sm p-4" id="formProfil">
          <h4 class="text-primary border-bottom pb-2 mb-3">Informasi Profil</h4>

          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" id="nama" class="form-control" placeholder="Nama Mahasiswa">
          </div>
          <div class="mb-3">
            <label class="form-label">NIM</label>
            <input type="text" id="nim" class="form-control" placeholder="4342101234">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="email" class="form-control" placeholder="contoh@polibatam.ac.id">
          </div>

          <div class="mb-3">
            <label class="form-label">Jurusan</label>
            <select id="jurusan" class="form-select">
              <option value="">-- Pilih Jurusan --</option>
              <option value="Teknik Informatika">Teknik Informatika</option>
              <option value="Teknik Elektro">Teknik Elektro</option>
              <option value="Teknik Mesin">Teknik Mesin</option>
              <option value="Manajemen dan Bisnis">Manajemen dan Bisnis</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Program Studi</label>
            <select id="prodi" class="form-select">
              <option value="">-- Pilih Program Studi --</option>
            </select>
          </div>

          <h4 class="text-primary border-bottom pb-2 mt-4 mb-3">Informasi Lainnya</h4>
          <div class="mb-3">
            <label class="form-label">Deskripsi Diri</label>
            <textarea id="deskripsi" class="form-control" rows="3"
              placeholder="Ceritakan tentang diri Anda..."></textarea>
          </div>

          <h4 class="text-primary border-bottom pb-2 mt-4 mb-3">Riwayat Pendidikan</h4>
          <div class="mb-3">
            <label class="form-label">SMA</label>
            <input type="text" id="sma" class="form-control" placeholder="Nama SMA">
          </div>
          <div class="mb-3">
            <label class="form-label">Tahun Lulus</label>
            <!-- Type diubah dari "text" menjadi "date" -->
            <input type="date" id="tahunLulus" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Universitas/Politeknik</label>
            <input type="text" id="univ" class="form-control" value="Politeknik Negeri Batam" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Kemampuan</label>
            <input type="text" id="kemampuan" class="form-control" placeholder="Hard Skills/Soft Skills">
          </div>
        </div>

        <!-- PREVIEW PROFIL -->
        <div class="card shadow-sm p-4 d-none" id="previewProfil">
          <h4 class="text-primary border-bottom pb-2 mb-3">Informasi Profil</h4>
          <p><strong>Nama:</strong> <span id="prevNama">-</span></p>
          <p><strong>NIM:</strong> <span id="prevNim">-</span></p>
          <p><strong>Email:</strong> <span id="prevEmail">-</span></p>
          <p><strong>Jurusan:</strong> <span id="prevJurusan">-</span></p>
          <p><strong>Program Studi:</strong> <span id="prevProdi">-</span></p>

          <h4 class="text-primary border-bottom pb-2 mt-4 mb-3">Informasi Lainnya</h4>
          <p><strong>Deskripsi Diri:</strong> <span id="prevDeskripsi">-</span></p>

          <h4 class="text-primary border-bottom pb-2 mt-4 mb-3">Riwayat Pendidikan</h4>
          <p><strong>SMA:</strong> <span id="prevSma">-</span></p>
          <p><strong>Tahun Lulus:</strong> <span id="prevTahunLulus">-</span></p>
          <p><strong>Universitas/Politeknik:</strong> <span id="prevUniv">Politeknik Negeri Batam</span></p>
          <p><strong>Kemampuan:</strong> <span id="prevKemampuan">-</span></p>

          <button id="kembaliEdit" class="btn btn-secondary mt-3">Kembali ke Edit Profil</button>
        </div>
      </section>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center text-white py-3 mt-5" style="background-color: #002b5b;">
    © 2025 PBL Polibatam | Tentang | Kontak | Kebijakan Privasi
  </footer>

  <!-- Script -->
  <script src="script_profil.js"></script>

</body>

</html>