<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['id_dosen']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_project = $_POST['id_project'] ?? null;
    $nilai = $_POST['nilai'] ?? null;
    $komentar = $_POST['komentar'] ?? '';
    $id_dosen = $_SESSION['id_dosen']; 
    
    if (!$id_project || !$nilai) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }
    
    try {
        $sql_check = "SELECT id FROM penilaian WHERE id_project = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$id_project]);
        $existing = $stmt_check->fetch();
        
        if ($existing) {
            $sql = "UPDATE penilaian 
                    SET nilai = ?, komentar = ?, id_dosen = ?, tanggal_dinilai = NOW() 
                    WHERE id_project = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nilai, $komentar, $id_dosen, $id_project]);
            $message = 'Penilaian berhasil diperbarui';
        } else {
            $sql = "INSERT INTO penilaian (id_project, id_dosen, nilai, komentar, tanggal_dinilai) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_project, $id_dosen, $nilai, $komentar]);
            $message = 'Penilaian berhasil disimpan';
        }
        
        echo json_encode(['success' => true, 'message' => $message]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid']);
}
?>