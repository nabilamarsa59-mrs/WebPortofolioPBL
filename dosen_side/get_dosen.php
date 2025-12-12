<?php
// get_dosen.php - PERBAIKAN PATH
session_start();

// Bersihkan output buffer
if (ob_get_length())
    ob_clean();

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// TUTUP error reporting untuk production
error_reporting(0);

// PERBAIKAN: Gunakan path yang benar ke koneksi.php
// Karena get_dosen.php ada di dosen_side/, dan koneksi.php ada di parent folder
require_once __DIR__ . '/../koneksi.php'; // KUNCI PERBAIKAN!

// Debug: Cek apakah koneksi berhasil
if (!isset($pdo) || !$pdo) {
    echo json_encode([
        'success' => false,
        'message' => 'Koneksi database tidak tersedia.',
        'debug' => 'PDO object not found'
    ]);
    exit();
}

// Cek session
$dosen_id = null;
if (isset($_SESSION['id_dosen'])) {
    $dosen_id = $_SESSION['id_dosen'];
} elseif (isset($_SESSION['id'])) {
    $dosen_id = $_SESSION['id'];
}

// Jika tidak ada session
if (!$dosen_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda belum login sebagai dosen.',
        'session_debug' => $_SESSION
    ]);
    exit();
}

try {
    // Query data dosen
    $sql = "SELECT id, nama_lengkap, nidn, email, jabatan, foto_profil 
            FROM dosen 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dosen) {
        // Query statistik
        $sql_portofolio = "SELECT COUNT(*) as total FROM penilaian WHERE id_dosen = ?";
        $stmt_portofolio = $pdo->prepare($sql_portofolio);
        $stmt_portofolio->execute([$dosen_id]);
        $stat_portofolio = $stmt_portofolio->fetch(PDO::FETCH_ASSOC);

        $sql_komentar = "SELECT COUNT(*) as total FROM penilaian 
                        WHERE id_dosen = ? AND komentar IS NOT NULL AND komentar != ''";
        $stmt_komentar = $pdo->prepare($sql_komentar);
        $stmt_komentar->execute([$dosen_id]);
        $stat_komentar = $stmt_komentar->fetch(PDO::FETCH_ASSOC);

        // Tambahkan statistik
        $dosen['stat_portofolio'] = $stat_portofolio['total'] ?? 0;
        $dosen['stat_komentar'] = $stat_komentar['total'] ?? 0;

        echo json_encode([
            'success' => true,
            'data' => $dosen
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data dosen tidak ditemukan.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>