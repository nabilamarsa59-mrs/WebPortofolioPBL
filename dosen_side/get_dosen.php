<?php
session_start();
require_once 'koneksi.php';

// PERBAIKAN: Sesuaikan nama variabel session
if (!isset($_SESSION['id_dosen'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// PERBAIKAN: Sesuaikan nama variabel session
 $dosen_id = $_SESSION['id_dosen'];

try {
    // Ambil data dosen
    $sql = "SELECT * FROM dosen WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $dosen = $stmt->fetch();

    if ($dosen) {
        echo json_encode(['success' => true, 'data' => $dosen]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data dosen tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>