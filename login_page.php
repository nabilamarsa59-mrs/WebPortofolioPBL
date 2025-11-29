<?php
// --- LOGIKA PHP (PROSES LOGIN) ---
// Memulai sesi untuk menyimpan informasi login
session_start();

// Menyimpan pesan error
$error_message = '';

// Cek apakah form telah disubmit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

// --- KONEKSI DATABASE ---
// Ganti dengan detail koneksi database Anda
$db_host = 'localhost';
$db_user = 'root'; // username MySQL Anda
$db_pass = ''; // password MySQL Anda
$db_name = 'nama_database_anda'; // nama database Anda

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$user_type = $_POST['user_type'];
$identifier = $_POST['identifier']; // Bisa NIM atau Username
$password = $_POST['password'];

// Siapkan variabel untuk hasil
$is_valid = false;
$user_data = null;

if ($user_type == 'mahasiswa') {
// Query untuk mahasiswa (gunakan NIM)
$sql = "SELECT nim, nama_mhs, password FROM login_mhs WHERE nim = ?";
} else {
// Query untuk dosen (gunakan username)
$sql = "SELECT username, nama_dsn, password FROM login_dsn WHERE username = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $identifier);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
$user_data = $result->fetch_assoc();

// VERIFIKASI PASSWORD
// Untuk keamanan, gunakan password_verify() jika password di-hash
// Contoh: if (password_verify($password, $user_data['password'])) { ... }
if ($password === $user_data['password']) {
$is_valid = true;
}
}

$stmt->close();
$conn->close();

if ($is_valid) {
// Jika login berhasil, set sesi
$_SESSION['loggedin'] = true;
$_SESSION['user_type'] = $user_type;
$_SESSION['user_name'] = ($user_type == 'mahasiswa') ? $user_data['nama_mhs'] : $user_data['nama_dsn'];
$_SESSION['user_identifier'] = $identifier; // NIM atau Username

// Arahkan ke halaman dashboard yang sesuai
if ($user_type == 'mahasiswa') {
header("location: home_mahasiswa.php");
} else {
header("location: home_dosen.php");
}
exit;
} else {
// Jika login gagal
$error_message = "NIM/Username atau Kata Sandi salah!";
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
/* --- Variabel Warna & Font --- */
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
/* Gunakan file lokal untuk background */
background: url('bg-gedung.jpg') no-repeat center center/cover;
background-color: var(--primary-color); /* Warna cadangan */
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
.alert-danger {
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

.register-link {
margin-top: 20px;
font-size: 0.9rem;
}

.register-link a {
color: var(--primary-color);
text-decoration: none;
font-weight: 600;
}

.register-link a:hover {
text-decoration: underline;
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
<div class="alert-danger"><?php echo $error_message; ?></div>
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
<input type="text" class="form-control" id="identifier" name="identifier" placeholder="Masukkan NIM Anda" required>
</div>

<!-- Input Password -->
<div class="form-group">
<label for="password">Kata Sandi</label>
<input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
</div>

<button type="submit" class="btn-login">Masuk</button>

<p class="register-link">Belum punya akun? <a href="register_page.php">Daftar sekarang</a></p>
</form>
</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
option.addEventListener('click', function() {
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