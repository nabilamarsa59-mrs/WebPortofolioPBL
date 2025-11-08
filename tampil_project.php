<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PoliKarya - Portofolio PBL Polibatam</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <style>
    /* Tambahkan styling footer biru tua */
    footer.custom-footer {
      background-color: #002b5b; /* biru tua */
      color: white;
    }
    footer.custom-footer a {
      color: white;
      text-decoration: none;
    }
    footer.custom-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body class="bg-light text-dark">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#002b5b;">
    <div class="container-fluid px-4">
      <a class="navbar-brand fw-bold" href="#">WorkPiece</a>
      <div>
        <a class="nav-link d-inline text-white" href="home_page.php">Beranda</a>
        <a class="nav-link d-inline text-white" href="profil_page.php">Profil</a>
      </div>
    </div>
  </nav>

  <!-- Container -->
  <div class="container my-5">
    <button onclick="goBack()" class="btn btn-secondary mb-3">Kembali</button>

    <div class="card shadow p-4">
      <h2 class="text-center mb-4">Hasil Proyek Mahasiswa</h2>

      <div class="mb-3">
        <label class="form-label fw-semibold">Judul Proyek</label>
        <input type="text" id="judulProyek" class="form-control" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Jurusan</label>
        <input type="text" id="jurusan" class="form-control" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi Proyek</label>
        <textarea id="deskripsiProyek" class="form-control" rows="4" readonly></textarea>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-body text-center">
              <p class="fw-bold">Foto</p>
              <img id="fotoProyek" alt="Foto Proyek" class="img-fluid rounded">
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-body text-center">
              <p class="fw-bold">Video</p>
              <div class="ratio ratio-16x9">
                <iframe id="videoProyek" allowfullscreen></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-4 text-center">
        <p id="tglMulai" class="mb-1 text-muted"></p>
        <p id="tglSelesai" class="text-muted"></p>
      </div>

      <div class="d-flex justify-content-center gap-3 mt-4">
        <button class="btn btn-warning fw-semibold" onclick="goBackUpload()">Ubah</button>
        <button class="btn btn-danger fw-semibold" onclick="hapusProyek()">Hapus</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="custom-footer py-3 mt-5">
    <div class="container text-center">
      <span>© 2025 PBL Polibatam</span>
      <span class="mx-2">|</span>
      <a href="#">Tentang</a>
      <span class="mx-2">|</span>
      <a href="#">Proyek</a>
      <span class="mx-2">|</span>
      <a href="#">Kebijakan Privasi</a>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script Proyek -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const data = JSON.parse(localStorage.getItem("projectData"));

      if (!data) {
        alert("Tidak ada data proyek ditemukan!");
        window.location.href = "upload_project.php";
        return;
      }

      document.getElementById("judulProyek").value = data.title;
      document.getElementById("jurusan").value = data.jurusan || "Belum diisi";
      document.getElementById("deskripsiProyek").value = data.description;
      document.getElementById("fotoProyek").src = data.photo;
      document.getElementById("tglMulai").textContent = "Tanggal Pembuatan: " + data.startDate;
      document.getElementById("tglSelesai").textContent = "Tanggal Selesai: " + data.endDate;

      if (data.videoLink) {
        let videoSrc = data.videoLink;
        if (videoSrc.includes("youtube.com/watch?v=")) {
          const videoId = videoSrc.split("v=")[1].split("&")[0];
          videoSrc = "https://www.youtube.com/embed/" + videoId;
        }
        document.getElementById("videoProyek").src = videoSrc;
      } else {
        document.getElementById("videoProyek").style.display = "none";
      }
    });

    function goBack() {
      window.location.href = "upload_project.php";
    }

    function goBackUpload() {
      localStorage.setItem("editMode", "true");
      window.location.href = "upload_project.php";
    }

    function hapusProyek() {
      if (confirm("Yakin ingin menghapus proyek ini?")) {
        localStorage.removeItem("projectData");
        window.location.href = "upload_project.php";
      }
    }
  </script>
</body>
</html>
