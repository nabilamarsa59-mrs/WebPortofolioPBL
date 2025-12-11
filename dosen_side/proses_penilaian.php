<?php
// PERBAIKAN: Gunakan koneksi dan session check yang standar
require_once '../koneksi.php';
session_start();

// Pastikan hanya dosen yang bisa mengakses file ini
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit();
}

header('Content-Type: application/json'); // Set header untuk response JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST request
    $id_project = $_POST['id_project'];
    $nilai = $_POST['nilai'];
    $komentar = $_POST['komentar'];

    // Validasi input sederhana
    if (empty($id_project) || empty($nilai)) {
        echo json_encode(['success' => false, 'message' => 'ID Proyek dan Nilai tidak boleh kosong.']);
        exit();
    }

    try {
        // Cek apakah penilaian untuk proyek ini sudah ada
        $checkSql = "SELECT id FROM penilaian WHERE id_project = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$id_project]);
        $existingGrade = $checkStmt->fetch();

        if ($existingGrade) {
            // Jika sudah ada, lakukan UPDATE
            $sql = "UPDATE penilaian SET nilai = ?, komentar = ? WHERE id_project = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nilai, $komentar, $id_project]);
        } else {
            // Jika belum ada, lakukan INSERT
            $sql = "INSERT INTO penilaian (id_project, nilai, komentar) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_project, $nilai, $komentar]);
        }

        echo json_encode(['success' => true, 'message' => 'Penilaian berhasil disimpan.']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>