<?php
// Memulai atau melanjutkan sesi yang ada
session_start();

// Cek apakah pengguna sudah login.
// Jika tidak ada session 'user_id' atau 'role', arahkan ke halaman login.
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Pengguna belum login, pindah ke halaman login
    header("Location: ../login.php"); // Sesuaikan path jika perlu
    exit(); // Hentikan eksekusi skrip
}

// (Opsional tapi sangat direkomendasikan) Cek role untuk halaman tertentu
// Contoh: untuk memastikan hanya dosen yang bisa akses halaman di folder dosen_side
 $current_file = $_SERVER['PHP_SELF'];
if (strpos($current_file, '/dosen_side/') !== false && $_SESSION['role'] !== 'dosen') {
    // Jika bukan dosen mencoba akses halaman dosen, arahkan ke halaman login atau halaman tidak sah
    header("Location: ../login.php");
    exit();
}

if (strpos($current_file, '/mahasiswa_side/') !== false && $_SESSION['role'] !== 'mahasiswa') {
    // Jika bukan mahasiswa mencoba akses halaman mahasiswa, arahkan ke halaman login
    header("Location: ../login.php");
    exit();
}
?>