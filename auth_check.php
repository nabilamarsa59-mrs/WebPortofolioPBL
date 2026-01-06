<?php
// Memulai session untuk mengakses data login pengguna
session_start();

// Mengecek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit(); 
}

// Menyimpan lokasi file PHP yang sedang diakses oleh user
 $current_file = $_SERVER['PHP_SELF'];

 // Mengecek akses halaman dosen
// Jika user membuka folder dosen_side tetapi rolenya bukan dosen,
// maka user akan diarahkan kembali ke halaman login
if (strpos($current_file, '/dosen_side/') !== false && $_SESSION['role'] !== 'dosen') {
    header("Location: ../login.php");
    exit();
}


// Mengecek akses halaman mahasiswa
// Jika user membuka folder mahasiswa_side tetapi rolenya bukan mahasiswa,
// maka user akan diarahkan kembali ke halaman login
if (strpos($current_file, '/mahasiswa_side/') !== false && $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}
?>