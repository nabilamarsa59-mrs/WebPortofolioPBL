<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['id_dosen'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

$dosen_id = $_SESSION['id_dosen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $jabatan = $_POST['jabatan'];
    $bio = $_POST['bio'];

    try {
        // Update database
        $sql = "UPDATE dosen SET nama_lengkap = ?, email = ?, jabatan = ?, bio = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama_lengkap, $email, $jabatan, $bio, $dosen_id]);

        echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
?>