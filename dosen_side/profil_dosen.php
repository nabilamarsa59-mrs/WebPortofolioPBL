<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    header("location:../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Dosen - WorkPiece</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: whitesmoke;
    padding-top: 100px;
}

/* ===== FOTO PROFIL ===== */
.profile-container {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto 1rem;
}

.profile-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #dcdcdc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 4px solid #fff;
    box-shadow: 0 0 10px rgba(0,0,0,.1);
}

.profile-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-icon {
    font-size: 4.5rem;
    color: #9aa0a6;
}

#change-photo-btn {
    position: absolute;
    bottom: 6px;
    right: 6px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0d6efd;
    border: 2px solid #fff;
}

#change-photo-btn i {
    color: #fff;
}

/* ===== LAINNYA ===== */
.navbar {
    background: #00003c;
}
.text-navy {
    color: #00003c;
}
.stat-number {
    font-size: 1.5rem;
    font-weight: 600;
    color: #00003c;
}
.activity-item {
    border-left: 4px solid #00003c;
    background: #f8f9fa;
    padding: 1rem;
    margin-bottom: .75rem;
}
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #eee;
    border-top: 5px solid #003366;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>

<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
<div class="container">
    <a class="navbar-brand" href="#">WorkPiece</a>
</div>
</nav>

<div class="container mt-4">
<div class="row g-4">

<!-- ===== PROFIL ===== -->
<div class="col-lg-4">
<div class="card shadow-sm">
<div class="card-body text-center">

<div class="profile-container">
    <div class="profile-avatar">
        <i class="bi bi-person-fill avatar-icon"></i>
        <img id="profile-image" class="profile-avatar-img d-none">
    </div>
    <button class="btn" id="change-photo-btn">
        <i class="bi bi-camera-fill"></i>
    </button>
</div>

<input type="file" id="profile-pic-input" accept="image/*" hidden>

<h5 class="text-navy mt-3" id="dosen-name">Loading...</h5>
<p class="small text-muted" id="dosen-nidn">NIDN: -</p>
<p class="small text-muted" id="dosen-bidang">-</p>
<p class="small text-muted" id="dosen-email">-</p>

</div>
</div>
</div>

<!-- ===== AKTIVITAS ===== -->
<div class="col-lg-8">
<div class="card shadow-sm">
<div class="card-body">
<h6 class="text-navy mb-3">Aktivitas Terakhir</h6>
<div id="activity-list"></div>
</div>
</div>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

const img = document.getElementById('profile-image');
const icon = document.querySelector('.avatar-icon');
const fileInput = document.getElementById('profile-pic-input');
const changeBtn = document.getElementById('change-photo-btn');

fetch('get_dosen.php')
.then(res => res.json())
.then(res => {
    if(res.success && res.data.foto_profil){
        img.src = res.data.foto_profil;
        img.classList.remove('d-none');
        icon.classList.add('d-none');
    }
    document.getElementById('dosen-name').textContent = res.data.nama_lengkap;
    document.getElementById('dosen-nidn').textContent = 'NIDN: ' + res.data.nidn;
    document.getElementById('dosen-bidang').textContent = res.data.bidang_keahlian;
    document.getElementById('dosen-email').textContent = res.data.email;
});

changeBtn.onclick = () => fileInput.click();

fileInput.onchange = () => {
    const file = fileInput.files[0];
    if(!file) return;

    const formData = new FormData();
    formData.append('foto_profil', file);

    fetch('upload_foto.php', { method:'POST', body: formData })
    .then(res => res.json())
    .then(res => {
        if(res.success){
            img.src = res.path + '?t=' + Date.now();
            img.classList.remove('d-none');
            icon.classList.add('d-none');
        }
    });
};

});
</script>

</body>
</html>
