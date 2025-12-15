<?php
session_start();
require_once '../koneksi.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID proyek tidak ditemukan']);
    exit();
}

$project_id = $_GET['id'];

$sql = "SELECT p.*, m.nama_lengkap, m.nim, m.jurusan
        FROM projects p
        JOIN mahasiswa m ON p.id_mahasiswa = m.id
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if ($project) {
    echo json_encode(['success' => true, 'data' => $project]);
} else {
    echo json_encode(['success' => false, 'message' => 'Proyek tidak ditemukan']);
}
?>