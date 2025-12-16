<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

 $id_project = $_GET['id'];

 $stmt_mahasiswa = $pdo->prepare("SELECT id_mahasiswa FROM users WHERE id = ?");
 $stmt_mahasiswa->execute([$_SESSION['user_id']]);
 $id_mahasiswa_login = $stmt_mahasiswa->fetchColumn();

 $stmt_project = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
 $stmt_project->execute([$id_project]);
 $project = $stmt_project->fetch();

if (!$project || $project['id_mahasiswa'] != $id_mahasiswa_login) {
    header("Location: home_mhs.php");
    exit();
}

if (!empty($project['gambar'])) {
    $file_path = '../uploads/' . $project['gambar'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

 $stmt_hapus = $pdo->prepare("DELETE FROM projects WHERE id = ?");
 $stmt_hapus->execute([$id_project]);

header("Location: home_mhs.php");
exit();
?>