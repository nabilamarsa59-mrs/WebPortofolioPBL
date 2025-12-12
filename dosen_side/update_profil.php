<?php
session_start();
require_once 'koneksi.php';

// Cek session
if (!isset($_SESSION['id_dosen']) && !isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// Set header JSON
header('Content-Type: application/json');

// Ambil ID dosen dari session
$dosen_id = isset($_SESSION['id_dosen']) ? $_SESSION['id_dosen'] : $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $jabatan = filter_input(INPUT_POST, 'jabatan', FILTER_SANITIZE_STRING);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email tidak valid']);
        exit();
    }
    
    try {
        // Update database
        $sql = "UPDATE dosen SET nama_lengkap = ?, email = ?, jabatan = ?, bio = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama_lengkap, $email, $jabatan, $bio, $dosen_id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada perubahan data']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
?>