<?php
session_start();
require_once '../koneksi.php';

header('Content-Type: application/json');

// Autentikasi dosen
if (!isset($_SESSION['id_dosen']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

$dosen_id = $_SESSION['id_dosen'];

// ambil data dosen
try {
    $sql = "SELECT * FROM dosen WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    // penyesuaian path foto profil
    if ($dosen) {
        if ($dosen['foto_profil'] && !empty($dosen['foto_profil'])) {
            if (strpos($dosen['foto_profil'], '../uploads') === false) {
                $dosen['foto_profil'] = '../uploads/dosen/' . $dosen['foto_profil'];
            }
        }
        
        echo json_encode(['success' => true, 'data' => $dosen]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data dosen tidak ditemukan']);
    }
} catch (PDOException $e) {
    // error handling
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>