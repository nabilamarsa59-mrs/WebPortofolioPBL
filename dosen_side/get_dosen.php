<?php
session_start();
require_once 'koneksi.php';

// Pastikan dosen sudah login
if (!isset($_SESSION['dosen_id'])) {
    header("Location: login.php");
    exit();
}

 $dosen_id = $_SESSION['dosen_id'];

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