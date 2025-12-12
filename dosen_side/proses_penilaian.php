<?php
// proses_penilaian.php
require_once '../koneksi.php';
session_start();

// Pastikan hanya dosen yang bisa mengakses file ini
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login kembali.']);
    exit();
}

// PERBAIKAN: Ambil ID dosen dari session
// Cek beberapa kemungkinan nama session ID dosen
$id_dosen = null;
if (isset($_SESSION['id_dosen'])) {
    $id_dosen = $_SESSION['id_dosen'];
} elseif (isset($_SESSION['id'])) {
    $id_dosen = $_SESSION['id'];
} elseif (isset($_SESSION['user_id'])) {
    $id_dosen = $_SESSION['user_id'];
}

if (!$id_dosen) {
    echo json_encode(['success' => false, 'message' => 'ID dosen tidak ditemukan dalam session.']);
    exit();
}

header('Content-Type: application/json'); // Set header untuk response JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari POST request dengan validasi
    $id_project = isset($_POST['id_project']) ? trim($_POST['id_project']) : null;
    $nilai = isset($_POST['nilai']) ? trim($_POST['nilai']) : null;
    $komentar = isset($_POST['komentar']) ? trim($_POST['komentar']) : '';

    // Validasi input
    if (empty($id_project) || empty($nilai)) {
        echo json_encode(['success' => false, 'message' => 'ID Proyek dan Nilai tidak boleh kosong.']);
        exit();
    }

    // Validasi nilai (hanya A, A-, B+, B, B-, C+, C, D, E yang diperbolehkan)
    $nilai_valid = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
    if (!in_array($nilai, $nilai_valid)) {
        echo json_encode(['success' => false, 'message' => 'Nilai tidak valid.']);
        exit();
    }

    try {
        // PERBAIKAN PENTING: Cek apakah proyek ada di database
        $checkProjectSql = "SELECT id FROM projects WHERE id = ?";
        $checkProjectStmt = $pdo->prepare($checkProjectSql);
        $checkProjectStmt->execute([$id_project]);
        $projectExists = $checkProjectStmt->fetch();

        if (!$projectExists) {
            echo json_encode(['success' => false, 'message' => 'Proyek tidak ditemukan.']);
            exit();
        }

        // PERBAIKAN: Cek apakah penilaian untuk proyek ini sudah ada (dengan id_dosen)
        $checkSql = "SELECT id FROM penilaian WHERE id_project = ? AND id_dosen = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$id_project, $id_dosen]);
        $existingGrade = $checkStmt->fetch();

        if ($existingGrade) {
            // Jika sudah ada, lakukan UPDATE
            $sql = "UPDATE penilaian SET nilai = ?, komentar = ?, tanggal = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nilai, $komentar, $existingGrade['id']]);
            $message = 'Penilaian berhasil diperbarui.';
        } else {
            // Jika belum ada, lakukan INSERT dengan id_dosen
            $sql = "INSERT INTO penilaian (id_project, id_dosen, nilai, komentar, tanggal) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_project, $id_dosen, $nilai, $komentar]);
            $message = 'Penilaian berhasil disimpan.';
        }

        // PERBAIKAN: Cek apakah query berhasil
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'data' => [
                    'id_project' => $id_project,
                    'nilai' => $nilai,
                    'komentar' => $komentar
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada perubahan data.']);
        }

    } catch (PDOException $e) {
        // PERBAIKAN: Jangan tampilkan detail error ke user, log saja
        error_log("Database error in proses_penilaian.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.']);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
}
?>