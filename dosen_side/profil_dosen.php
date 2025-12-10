<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Dosen - WorkPiece</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            background-color: whitesmoke;
            padding-top: 100px;
        }

        .navbar {
            background: rgba(0, 0, 60, 0.8) !important;
            padding: 0.75rem 0;
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

        .navbar a:hover {
            text-decoration: underline;
        }

        /* --- PERUBAHAN & TAMBAHAN CSS --- */
        .profile-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
        }

        /* Style untuk placeholder ikon dan gambar */
        .profile-avatar,
        .profile-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            /* Hanya untuk <img> */
        }

        /* Style khusus untuk placeholder ikon */
        .profile-avatar {
            background: rgba(0, 0, 60, 0.8) !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #change-photo-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #change-photo-btn:hover {
            transform: scale(1.1);
            background-color: #0066cc;
        }

        /* --- AKHIR PERUBAHAN CSS --- */

        .text-navy {
            color: #00003c !important;
        }

        .stat-number {
            font-size: 1.5rem;
            color: var(--navy-blue);
            font-weight: 600;
        }

        .activity-item {
            border-left: 4px solid var(--navy-blue);
            background-color: #f8f9fa;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: transform 0.2s ease;
        }

        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        .activity-item p {
            margin: 0;
        }

        .activity-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        /* Toast notification styling */
        .toast-container {
            position: fixed;
            top: 110px;
            right: 20px;
            z-index: 1050;
        }

        .custom-toast {
            min-width: 250px;
        }

        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #003366;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Profile info styling */
        .profile-info {
            transition: all 0.2s ease;
        }

        .profile-info:hover {
            background-color: rgba(0, 0, 60, 0.05);
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">WorkPiece</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#profil">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home_dosen.html">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row g-4">
            <!-- Left Column - Profile Card -->
            <div class="col-lg-4">
                <!-- Profile Card -->
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <!-- Container untuk avatar (ikon atau gambar) -->
                        <div class="profile-container">
                            <!-- Default: Ikon Profil -->
                            <div id="avatar-placeholder" class="profile-avatar">
                                <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                            </div>

                            <button class="btn btn-primary" id="change-photo-btn" title="Ganti Foto Profil">
                                <i class="bi bi-camera-fill text-white"></i>
                            </button>
                        </div>
                        <!-- Input file disembunyikan -->
                        <input type="file" id="profile-pic-input" accept="image/*" style="display: none;">

                        <h5 class="text-navy mb-2 mt-3">Dr. Budi Santoso, M.Kom</h5>
                        <p class="text-muted small mb-1">NIDN: 198501012010121001</p>
                        <p class="text-muted small mb-4">Lektor Kepala</p>

                        <div class="border-top pt-3">
                            <div class="profile-info d-flex align-items-center mb-2 small">
                                <i class="bi bi-envelope text-navy me-2"></i>
                                <span class="text-muted">budi.santoso@univ.ac.id</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h6 class="text-navy mb-3">Statistik Aktivitas</h6>

                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-earmark-check text-navy me-2"></i>
                                <span class="small text-muted">Portofolio Dinilai</span>
                            </div>
                            <span class="stat-number">156</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-chat-square-text text-navy me-2"></i>
                                <span class="small text-muted">Total Komentar</span>
                            </div>
                            <span class="stat-number">342</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Activity -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-navy mb-4">
                            <i class="bi bi-clock-history me-2"></i>
                            Aktivitas Terakhir
                        </h6>

                        <div id="activity-list">
                            <div class="activity-item">
                                <p class="small text-dark">Memberikan nilai A untuk portofolio Ahmad Rizki</p>
                                <p class="activity-time">2 jam yang lalu</p>
                            </div>

                            <div class="activity-item">
                                <p class="small text-dark">Berkomentar pada portofolio Siti Nurhaliza</p>
                                <p class="activity-time">5 jam yang lalu</p>
                            </div>

                            <div class="activity-item">
                                <p class="small text-dark">Memberikan nilai B+ untuk portofolio Budi Prasetyo</p>
                                <p class="activity-time">1 hari yang lalu</p>
                            </div>

                            <div class="activity-item">
                                <p class="small text-dark">Berkomentar pada portofolio Dewi Lestari</p>
                                <p class="activity-time">1 hari yang lalu</p>
                            </div>

                            <div class="activity-item">
                                <p class="small text-dark">Memberikan nilai A- untuk portofolio Andi Wijaya</p>
                                <p class="activity-time">2 hari yang lalu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // JavaScript untuk mengambil data dosen dari server
        document.addEventListener('DOMContentLoaded', function () {
            // Ambil data dosen dari server
            fetch('get_dosen.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update profil dosen
                        document.querySelector('.text-navy').textContent = data.data.nama_lengkap;
                        document.querySelector('.text-muted.mb-1').textContent = 'NIDN: ' + data.data.nidn;
                        document.querySelector('.text-muted.mb-4').textContent = data.data.jabatan;
                        document.querySelector('.profile-info .text-muted').textContent = data.data.email;

                        // Load foto profil jika ada
                        if (data.data.foto_profil) {
                            const img = document.createElement('img');
                            img.src = data.data.foto_profil;
                            img.alt = 'Profile Picture';
                            img.className = 'profile-avatar-img';
                            document.getElementById('avatar-placeholder').replaceWith(img);
                        }
                    } else {
                        console.error('Error:', data.message);
                        // Tampilkan pesan error di UI jika perlu
                        showToast(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Terjadi kesalahan saat memuat data', 'danger');
                });

            // Mendapatkan elemen-elemen yang dibutuhkan
            const changePhotoBtn = document.getElementById('change-photo-btn');
            const profilePicInput = document.getElementById('profile-pic-input');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Event listener untuk tombol "Ganti Foto"
            changePhotoBtn.addEventListener('click', () => {
                profilePicInput.click();
            });

            // Event listener untuk input file
            profilePicInput.addEventListener('change', function (event) {
                const file = event.target.files[0];

                if (file && file.type.startsWith('image/')) {
                    showLoading();

                    const formData = new FormData();
                    formData.append('foto_profil', file);

                    fetch('upload_foto.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            hideLoading();

                            if (data.success) {
                                // Update foto profil
                                const newImg = document.createElement('img');
                                newImg.src = data.path;
                                newImg.alt = 'Profile Picture';
                                newImg.className = 'profile-avatar-img';

                                const currentImg = document.querySelector('.profile-avatar-img');
                                if (currentImg) {
                                    currentImg.replaceWith(newImg);
                                } else {
                                    document.getElementById('avatar-placeholder').replaceWith(newImg);
                                }

                                showToast('Foto profil berhasil diperbarui!', 'success');
                            } else {
                                showToast(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            hideLoading();
                            console.error('Error:', error);
                            showToast('Terjadi kesalahan saat mengupload foto', 'danger');
                        });
                } else if (file) {
                    showToast('Silakan pilih file gambar yang valid.', 'danger');
                }
            });

            // Fungsi untuk menampilkan loading overlay
            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            // Fungsi untuk menyembunyikan loading overlay
            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            // Fungsi untuk menampilkan toast notifikasi
            function showToast(message, type = 'success') {
                const toastContainer = document.querySelector('.toast-container');
                const toastId = 'toast-' + Date.now();

                const toastHtml = `
            <div id="${toastId}" class="toast custom-toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
                toast.show();

                // Hapus elemen toast setelah disembunyikan
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            }
        });
    </script>
</body>

</html>