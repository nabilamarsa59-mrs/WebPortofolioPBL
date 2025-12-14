<?php
session_start();
require_once 'koneksi.php';

// Cek session dosen
if (!isset($_SESSION['id_dosen'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

$dosen_id = $_SESSION['id_dosen'];

header('Content-Type: application/json');

try {
    // Ambil data dosen
    $sql = "SELECT id, nama_lengkap, nidn, email, jabatan, foto_profil FROM dosen WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dosen) {
        // Ambil statistik
        $sql_portofolio = "SELECT COUNT(*) as total FROM penilaian WHERE id_dosen = ?";
        $stmt_portofolio = $pdo->prepare($sql_portofolio);
        $stmt_portofolio->execute([$dosen_id]);
        $portofolio = $stmt_portofolio->fetch(PDO::FETCH_ASSOC);
        
        $dosen['stat_portofolio'] = $portofolio['total'] ?? 0;
        $dosen['stat_komentar'] = $portofolio['total'] ?? 0; // Asumsi sama dengan portofolio
        
        echo json_encode(['success' => true, 'data' => $dosen]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data dosen tidak ditemukan']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>