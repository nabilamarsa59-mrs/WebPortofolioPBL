<?php
session_start();
require_once '../koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID Proyek dari URL
$id_project = $_GET['id'];

// Ambil ID Mahasiswa yang sedang login
$stmt_mahasiswa = $pdo->prepare("SELECT id_mahasiswa FROM users WHERE id = ?");
$stmt_mahasiswa->execute([$_SESSION['user_id']]);
$id_mahasiswa_login = $stmt_mahasiswa->fetchColumn();

// Ambil data proyek yang ingin dihapus
$stmt_project = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt_project->execute([$id_project]);
$project = $stmt_project->fetch();

// Validasi: Pastikan proyek ini milik mahasiswa yang login
if (!$project || $project['id_mahasiswa'] != $id_mahasiswa_login) {
    header("Location: home_mhs.php");
    exit();
}

// Cek Status Penilaian
// Jika proyek sudah dinilai, maka tidak boleh dihapus
$stmt_penilaian = $pdo->prepare("SELECT id FROM penilaian WHERE id_project = ?");
$stmt_penilaian->execute([$id_project]);
$penilaian = $stmt_penilaian->fetch();

if ($penilaian) {
    header("Location: home_mhs.php?error=sudah_dinilai_hapus");
    exit();
}

// Hapus Foto Proyek (Jika ada)
if (!empty($project['gambar'])) {
    $file_path = '../uploads/' . $project['gambar'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Hapus Data Proyek dari Database
$stmt_hapus = $pdo->prepare("DELETE FROM projects WHERE id = ?");
$stmt_hapus->execute([$id_project]);

// Redirect kembali ke Home setelah sukses
header("Location: home_mhs.php");
exit();
?>