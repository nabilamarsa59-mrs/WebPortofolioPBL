<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WorkPiece</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }

    .navbar {
      background-color: #002b5b !important;
    }

    .navbar a {
      color: #fff !important;
      text-decoration: none;
      margin-left: 15px;
    }

    .navbar a:hover {
      text-decoration: underline;
    }

    .form-section {
      border-bottom: 2px solid #e9ecef;
      padding-bottom: 1rem;
      margin-bottom: 1.5rem;
    }

    footer {
      background-color: #002b5b;
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
    }

    .upload-box {
      border: 2px dashed #ccc;
      border-radius: 8px;
      text-align: center;
      padding: 30px;
      cursor: pointer;
      background-color: #fafafa;
    }

    .upload-box:hover {
      background-color: #f1f7ff;
      border-color: #002b5b;
    }

    #preview img {
      margin-top: 10px;
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark px-5">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">WorkPiece</a>
      <div class="d-flex">
        <a href="home_page.php" class="nav-link">Beranda</a>
        <a href="profil_page.php" class="nav-link">Profil</a>
      </div>
    </div>
  </nav>

  <!-- Main Form -->
  <main class="container my-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-primary">Tambah Proyek PBL</h2>
      <p class="text-muted mb-4">Isi informasi proyek Anda dengan lengkap</p>

      <form id="projectForm">
        <!-- Informasi Dasar -->
        <div class="form-section">
          <h4 class="mb-3 text-primary">Informasi Dasar</h4>
          <div class="mb-3">
            <label for="judul" class="form-label fw-semibold">Judul Proyek</label>
            <input type="text" id="judul" class="form-control" placeholder="Masukkan judul proyek" required>
          </div>

          <div class="mb-3">
            <label for="deskripsi" class="form-label fw-semibold">Deskripsi Proyek</label>
            <textarea id="deskripsi" rows="4" class="form-control"
              placeholder="Jelaskan detail proyek, tujuan, dan hasil..." required></textarea>
          </div>

          <div class="mb-3">
            <label for="link" class="form-label fw-semibold">Link Video (Opsional)</label>
            <input type="url" id="link" class="form-control" placeholder="https://youtube.com/watch?v=...">
          </div>
        </div>

        <!-- Upload Foto -->
        <div class="form-section">
          <h4 class="mb-3 text-primary">Foto Proyek</h4>
          <div class="upload-box" id="uploadBox">
            <input type="file" id="foto" accept=".jpg,.jpeg,.png" hidden>
            <p class="text-secondary mb-0">
              Klik untuk upload atau drag & drop<br>
              <small>JPG, JPEG, PNG (max 30MB)</small>
            </p>
            <div id="preview"></div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="form-section">
          <h4 class="mb-3 text-primary">Timeline</h4>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="mulai" class="form-label fw-semibold">Tanggal Mulai</label>
              <input type="date" id="mulai" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label for="selesai" class="form-label fw-semibold">Tanggal Selesai</label>
              <input type="date" id="selesai" class="form-control" required>
            </div>
          </div>
        </div>

        <!-- Checklist -->
        <div class="form-section">
          <h4 class="mb-3 text-primary">Checklist Pengisian</h4>
          <ul class="list-group" id="checklist">
            <li class="list-group-item"><input type="checkbox" disabled> Judul proyek</li>
            <li class="list-group-item"><input type="checkbox" disabled> Deskripsi proyek</li>
            <li class="list-group-item"><input type="checkbox" disabled> Foto proyek</li>
            <li class="list-group-item"><input type="checkbox" disabled> Tanggal mulai</li>
            <li class="list-group-item"><input type="checkbox" disabled> Tanggal selesai</li>
          </ul>
        </div>

        <!-- Tombol -->
        <button type="submit" class="btn btn-primary w-100">Simpan Proyek</button>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <p>© 2025 PBL Polibatam | Tentang | Proyek | Kebijakan Privasi</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>>
  <script>
    // --- Mode Edit ---
    document.addEventListener("DOMContentLoaded", function () {
      if (localStorage.getItem("editMode") === "true") {
        const savedData = JSON.parse(localStorage.getItem("projectData"));
        if (savedData) {
          document.getElementById("judul").value = savedData.title;
          document.getElementById("deskripsi").value = savedData.description;
          document.getElementById("mulai").value = savedData.startDate;
          document.getElementById("selesai").value = savedData.endDate;
          if (savedData.photo) {
            const img = document.createElement("img");
            img.src = savedData.photo;
            document.getElementById("preview").appendChild(img);
          }
          if (savedData.videoLink) document.getElementById("link").value = savedData.videoLink;
          updateChecklist();
        }
      }
    });

    // Checklist Otomatis
    const form = document.getElementById('projectForm');
    const checklistItems = document.querySelectorAll('#checklist input[type="checkbox"]');
    const fields = {
      judul: document.getElementById('judul'),
      deskripsi: document.getElementById('deskripsi'),
      foto: document.getElementById('foto'),
      mulai: document.getElementById('mulai'),
      selesai: document.getElementById('selesai')
    };

    function updateChecklist() {
      const savedData = JSON.parse(localStorage.getItem("projectData"));
      checklistItems[0].checked = fields.judul.value.trim() !== '';
      checklistItems[1].checked = fields.deskripsi.value.trim() !== '';
      checklistItems[2].checked = fields.foto.files.length > 0 || (savedData && savedData.photo);
      checklistItems[3].checked = fields.mulai.value !== '';
      checklistItems[4].checked = fields.selesai.value !== '';
    }

    Object.values(fields).forEach(f => f.addEventListener('input', updateChecklist));

    // Upload Box
    const uploadBox = document.getElementById('uploadBox');
    const fotoInput = document.getElementById('foto');
    const preview = document.getElementById('preview');

    uploadBox.addEventListener('click', () => fotoInput.click());
    uploadBox.addEventListener('dragover', e => { e.preventDefault(); uploadBox.style.borderColor = '#002b5b'; });
    uploadBox.addEventListener('dragleave', () => uploadBox.style.borderColor = '#ccc');
    uploadBox.addEventListener('drop', e => {
      e.preventDefault();
      uploadBox.style.borderColor = '#ccc';
      fotoInput.files = e.dataTransfer.files;
      if (fotoInput.files.length > 0) showPreview(fotoInput.files[0]);
      updateChecklist();
    });

    function showPreview(file) {
      const reader = new FileReader();
      reader.onload = e => {
        preview.innerHTML = "";
        const img = document.createElement('img');
        img.src = e.target.result;
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    }

    fotoInput.addEventListener('change', () => {
      if (fotoInput.files.length > 0) {
        showPreview(fotoInput.files[0]);
        updateChecklist();
      }
    });

    // Simpan Form
    form.addEventListener('submit', e => {
      e.preventDefault();
      let videoLink = document.getElementById('link').value.trim();
      if (videoLink.includes("youtube.com/watch?v=")) {
        const videoId = videoLink.split("v=")[1].split("&")[0];
        videoLink = `https://www.youtube.com/embed/${videoId}`;
      } else if (videoLink.includes("youtu.be/")) {
        const videoId = videoLink.split("youtu.be/")[1].split("?")[0];
        videoLink = `https://www.youtube.com/embed/${videoId}`;
      }
      const projectData = {
        title: fields.judul.value.trim(),
        description: fields.deskripsi.value.trim(),
        photo: preview.querySelector('img') ? preview.querySelector('img').src : '',
        startDate: fields.mulai.value,
        endDate: fields.selesai.value,
        jurusan: "Teknik Informatika",
        videoLink: videoLink
      };
      localStorage.setItem("projectData", JSON.stringify(projectData));
      localStorage.removeItem("editMode");
      window.location.href = "tampil_project.php";
    });
  </script>
</body>

</html>