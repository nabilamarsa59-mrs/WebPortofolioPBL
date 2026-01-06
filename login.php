<!DOCTYPE html>
<html lang="id">
<!-- Halaman login WorkPiece -->

<head>
    <!-- Pengaturan karakter dan responsivitas -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WorkPiece</title>

    <!-- Import font, Bootstrap, dan icon -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Styling halaman login -->
    <style>
        /* Variabel warna global */
        :root {
            --primary-color: #003366;
            --secondary-color: #001F3F;
            --accent-color: #55bddd;
            --light-color: #ffffff;
            --text-dark: #333333;
            --error-color: #dc3545;
            --font-family: 'Poppins', sans-serif;
        }

        /* Styling body dengan background dan overlay */
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

        /* Wrapper utama login */
        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        /* Card login */
        .login-container {
            background-color: var(--light-color);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        /* Logo dan judul */
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
    </style>
</head>

<body>

    <!-- Container login -->
    <div class="login-wrapper">
        <div class="login-container">

            <!-- Logo -->
            <div class="logo">WorkPiece</div>
            <h2 class="login-title">Selamat Datang</h2>

            <!-- Menampilkan pesan error jika ada -->
            <?php if (!empty($error_message)): ?>
                <div class="alert-danger-custom">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Form login -->
            <form action="login.php" method="post" id="loginForm">
                <!-- Input hidden untuk menyimpan tipe user -->
                <input type="hidden" name="user_type" id="user_type_input" value="mahasiswa">

                <!-- Pilihan tipe user -->
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

                <!-- Input NIM / Username -->
                <div class="form-group">
                    <label for="identifier" id="identifierLabel">NIM</label>
                    <input type="text" class="form-control" id="identifier" name="identifier"
                        placeholder="Masukkan NIM Anda" required>
                </div>

                <!-- Input password -->
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan kata sandi" required>
                </div>

                <!-- Tombol login -->
                <button type="submit" class="btn-login">Masuk</button>
            </form>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script untuk mengganti tipe user -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userTypeOptions = document.querySelectorAll('.user-type-option');
            const userTypeInput = document.getElementById('user_type_input');
            const identifierLabel = document.getElementById('identifierLabel');
            const identifierInput = document.getElementById('identifier');

            // Fungsi untuk mengatur tampilan form berdasarkan tipe user
            function updateFormState(userType) {
                userTypeOptions.forEach(opt => opt.classList.remove('active'));
                document.querySelector(`[data-type="${userType}"]`).classList.add('active');

                userTypeInput.value = userType;

                if (userType === 'mahasiswa') {
                    identifierLabel.textContent = 'NIM';
                    identifierInput.placeholder = 'Masukkan NIM Anda';
                } else { 
                    identifierLabel.textContent = 'Username';
                    identifierInput.placeholder = 'Masukkan username Anda';
                }
            }

            // Event klik untuk pilihan user
            userTypeOptions.forEach(option => {
                option.addEventListener('click', function () {
                    const selectedType = this.getAttribute('data-type');
                    updateFormState(selectedType);
                });
            });

            // Default user adalah mahasiswa
            updateFormState('mahasiswa');
        });
    </script>
</body>

</html>
