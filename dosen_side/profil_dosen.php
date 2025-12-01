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
        :root {
            --navy-blue: #1e3a5f;
            --navy-dark: #2d4a6f;
        }

        body {
            background-color: #f8f9fa;
        }

        .navbar-custom {
            background-color: var(--navy-blue);
        }

        .text-navy {
            color: var(--navy-blue);
        }

        .bg-navy {
            background-color: var(--navy-blue);
        }

        .border-navy {
            border-color: var(--navy-blue) !important;
        }

        .profile-avatar {
            width: 128px;
            height: 128px;
            background-color: var(--navy-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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
        }

        .activity-item p {
            margin: 0;
        }

        .activity-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">WorkPiece - Profil Dosen</span>
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
                        <div class="profile-avatar mx-auto mb-3">
                            <i class="bi bi-person-fill text-white" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-navy mb-2">Dr. Budi Santoso, M.Kom</h5>
                        <p class="text-muted small mb-1">NIP: 198501012010121001</p>
                        <p class="text-muted small mb-4">Lektor Kepala</p>

                        <div class="border-top pt-3">
                            <div class="d-flex align-items-center mb-2 small">
                                <i class="bi bi-envelope text-navy me-2"></i>
                                <span class="text-muted">budi.santoso@univ.ac.id</span>
                            </div>
                            <div class="d-flex align-items-center small">
                                <i class="bi bi-telephone text-navy me-2"></i>
                                <span class="text-muted">+62 812-3456-7890</span>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
