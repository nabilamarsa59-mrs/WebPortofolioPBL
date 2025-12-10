<?php
// --- 1. CEK KEAMANAN ---
// Pastikan hanya user mahasiswa yang sudah login yang bisa mengakses file ini.
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// --- 2. AMBIL ID PROYEK DAN VERIFIKASI KEPEMILIKAN ---
// Ambil ID project dari URL
 $id_project = $_GET['id'];

// Ambil data mahasiswa yang sedang login
 $stmt_mahasiswa = $pdo->prepare("SELECT id_mahasiswa FROM users WHERE id = ?");
 $stmt_mahasiswa->execute([$_SESSION['user_id']]);
 $id_mahasiswa_login = $stmt_mahasiswa->fetchColumn();

// Ambil data project yang akan dihapus
 $stmt_project = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
 $stmt_project->execute([$id_project]);
 $project = $stmt_project->fetch();

// --- 3. CEK: APAKAH PROYEK INI MILIK MAHASISWA YANG LOGIN? ---
// Jika project tidak ditemukan ATAU id_mahasiswa di project tidak sama dengan id_mahasiswa yang login, maka tolak!
if (!$project || $project['id_mahasiswa'] != $id_mahasiswa_login) {
    // Arahkan kembali ke dashboard jika mencoba menghapus proyek orang lain
    header("Location: home_mhs.php");
    exit();
}

// --- 4. PROSES HAPUS ---
// Hapus file gambar dari folder 'uploads' jika ada
if (!empty($project['gambar'])) {
    $file_path = '../uploads/' . $project['gambar'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Hapus data proyek dari database
 $stmt_hapus = $pdo->prepare("DELETE FROM projects WHERE id = ?");
 $stmt_hapus->execute([$id_project]);

// --- 5. REDIRECT KEMBALI ---
// Arahkan kembali ke halaman dashboard mahasiswa
header("Location: home_mhs.php");
exit();
?>