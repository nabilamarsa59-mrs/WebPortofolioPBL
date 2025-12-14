<?php
// 1. Memulai Sesi
session_start();

// 2. Menghubungkan ke Database
require_once 'koneksi.php';

// 3. Inisialisasi Variabel Pesan Error
$error_message = '';

// 4. Cek Apakah Form Telah Dikirim (Metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 5. Ambil Data dari Form
    $user_type = $_POST['user_type'];
    $identifier = $_POST['identifier']; // Bisa NIM atau Username
    $password = $_POST['password'];

    // 6. Validasi Input
    if (empty($user_type) || empty($identifier) || empty($password)) {
        $error_message = "Semua field harus diisi.";
    } else {
        // 7. Query Database Berdasarkan Tipe User
        try {
            $user = null; // Inisialisasi variabel user

            // --- KASUS 1: JIKA YANG LOGIN ADALAH MAHASISWA ---
            if ($user_type == 'mahasiswa') {
                // Query untuk mengambil data mahasiswa berdasarkan NIM
                $sql = "SELECT u.id as user_id, u.password, u.role, m.nim
                        FROM users u
                        JOIN mahasiswa m ON u.id_mahasiswa = m.id
                        WHERE m.nim = ? AND u.role = 'mahasiswa'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$identifier]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // --- KASUS 2: JIKA YANG LOGIN ADALAH DOSEN ---
            } elseif ($user_type == 'dosen') {
                // Query untuk mengambil data dosen berdasarkan username
                $sql = "SELECT u.id as user_id, u.password, u.role, u.id_dosen
                        FROM users u
                        WHERE u.username = ? AND u.role = 'dosen'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$identifier]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // 8. Verifikasi User dan Password
// Periksa apakah user ditemukan dan passwordnya cocok
            if ($user && $password === $user['password']) {
                // Jika berhasil, simpan data ke sesi
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];

                // --- TAMBAHKAN DUA BARIS INI ---
                $_SESSION['status'] = "login";
                $_SESSION['level'] = $user['role'];

                // Simpan identifier yang spesifik untuk setiap role
                if ($user['role'] == 'dosen') {
                    $_SESSION['id_dosen'] = $user['id_dosen'];
                } else {
                    $_SESSION['nim'] = $user['nim'];
                }

                // Arahkan ke halaman dashboard yang sesuai
                if ($user['role'] == 'dosen') {
                    header("Location: dosen_side/home_dosen.php");
                } else {
                    header("Location: mahasiswa_side/home_mhs.php");
                }
                exit(); // Hentikan skrip setelah redirect

            } else {
                // Jika user tidak ditemukan atau password salah
                $error_message = "NIM/Username atau Kata Sandi salah.";
            }

        } catch (PDOException $e) {
            // Jika terjadi error pada database
            $error_message = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WorkPiece</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #003366;
            --secondary-color: #001F3F;
            --accent-color: #55bddd;
            --light-color: #ffffff;
            --text-dark: #333333;
            --error-color: #dc3545;
            --font-family: 'Poppins', sans-serif;
        }

        /* --- Styling Dasar --- */
        body {
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: url('bg-gedung.jpg') no-repeat center center/cover;
            background-color: var(--primary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Overlay gelap untuk kontras teks */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        /* --- Kotak Login Utama --- */
        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-container {
            background-color: var(--light-color);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .logo {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .login-title {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 30px;
        }

        /* --- Pemilih Tipe User --- */
        .user-type-selector {
            display: flex;
            background-color: #f0f0f0;
            border-radius: 50px;
            padding: 5px;
            margin-bottom: 25px;
        }

        .user-type-option {
            flex: 1;
            padding: 12px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #777;
        }

        .user-type-option.active {
            background-color: var(--primary-color);
            color: var(--light-color);
            box-shadow: 0 4px 10px rgba(0, 51, 102, 0.3);
        }

        .user-type-option i {
            font-size: 1.2rem;
            display: block;
            margin-bottom: 5px;
        }

        /* --- Form Input --- */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.25);
            outline: none;
        }

        /* --- Alert Error --- */
        .alert-danger-custom {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* --- Tombol --- */
        .btn-login {
            width: 100%;
            background-color: var(--primary-color);
            color: var(--light-color);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
        }

        /* --- Responsivitas --- */
        @media (max-width: 576px) {
            .login-container {
                padding: 30px 25px;
            }

            .logo {
                font-size: 1.9rem;
            }

            .user-type-option i {
                font-size: 1rem;
            }

            .user-type-option span {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="logo">WorkPiece</div>
            <h2 class="login-title">Selamat Datang</h2>

            <!-- Tampilkan pesan error jika ada -->
            <?php if (!empty($error_message)): ?>
                <div class="alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="post" id="loginForm">
                <!-- Input tersembunyi untuk menyimpan tipe user -->
                <input type="hidden" name="user_type" id="user_type_input" value="mahasiswa">

                <!-- Pilihan Tipe User -->
                <div class="user-type-selector">
                    <div class="user-type-option active" data-type="mahasiswa">
                        <i class="bi bi-mortarboard-fill"></i>
                        <span>Mahasiswa</span>
                    </div>
                    <div class="user-type-option" data-type="dosen">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Dosen</span>
                    </div>
                </div>

                <!-- Input NIM/Username -->
                <div class="form-group">
                    <label for="identifier" id="identifierLabel">NIM</label>
                    <input type="text" class="form-control" id="identifier" name="identifier"
                        placeholder="Masukkan NIM Anda" required>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan kata sandi" required>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userTypeOptions = document.querySelectorAll('.user-type-option');
            const userTypeInput = document.getElementById('user_type_input');
            const identifierLabel = document.getElementById('identifierLabel');
            const identifierInput = document.getElementById('identifier');

            function updateFormState(userType) {
                // Perbarui tampilan tombol pilihan
                userTypeOptions.forEach(opt => opt.classList.remove('active'));
                document.querySelector(`[data-type="${userType}"]`).classList.add('active');

                // Perbarui nilai input tersembunyi
                userTypeInput.value = userType;

                // Perbarui label dan placeholder
                if (userType === 'mahasiswa') {
                    identifierLabel.textContent = 'NIM';
                    identifierInput.placeholder = 'Masukkan NIM Anda';
                } else { // Dosen
                    identifierLabel.textContent = 'Username';
                    identifierInput.placeholder = 'Masukkan username Anda';
                }
            }

            // Event listener untuk pilihan tipe user
            userTypeOptions.forEach(option => {
                option.addEventListener('click', function () {
                    const selectedType = this.getAttribute('data-type');
                    updateFormState(selectedType);
                });
            });
            // Set state awal saat halaman dimuat
            updateFormState('mahasiswa');
        });
    </script>
</body>
</html>