<?php
session_start();
require_once 'koneksi.php'; // Pastikan path ini benar

// Debug: Cek session yang aktif
error_log("Session status: " . print_r($_SESSION, true));

// Cek session - perbaikan untuk berbagai kemungkinan nama session
$dosen_id = null;

// Coba beberapa kemungkinan nama session
if (isset($_SESSION['id_dosen'])) {
    $dosen_id = $_SESSION['id_dosen'];
} elseif (isset($_SESSION['id'])) {
    $dosen_id = $_SESSION['id'];
} elseif (isset($_SESSION['user_id'])) {
    $dosen_id = $_SESSION['user_id'];
}

// Debug: Log ID dosen yang ditemukan
error_log("Dosen ID found: " . $dosen_id);

if (!$dosen_id) {
    // Return JSON error dengan header yang benar
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Session dosen tidak ditemukan. Silakan login kembali.',
        'debug' => ['session' => $_SESSION]
    ]);
    exit();
}

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Debug: Log query yang akan dijalankan
    error_log("Querying dosen with ID: " . $dosen_id);
    
    // Ambil data dosen
    $sql = "SELECT id, nama_lengkap, nidn, email, jabatan, foto_profil FROM dosen WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dosen) {
        // Debug: Log data yang ditemukan
        error_log("Dosen data found: " . print_r($dosen, true));
        
        // Ambil statistik
        // PERBAIKAN: Query statistik yang benar
        $sql_portofolio = "SELECT COUNT(*) as total FROM penilaian WHERE id_dosen = ?";
        $stmt_portofolio = $pdo->prepare($sql_portofolio);
        $stmt_portofolio->execute([$dosen_id]);
        $stat_portofolio = $stmt_portofolio->fetch(PDO::FETCH_ASSOC);
        
        $sql_komentar = "SELECT COUNT(*) as total FROM penilaian WHERE id_dosen = ? AND komentar IS NOT NULL AND komentar != ''";
        $stmt_komentar = $pdo->prepare($sql_komentar);
        $stmt_komentar->execute([$dosen_id]);
        $stat_komentar = $stmt_komentar->fetch(PDO::FETCH_ASSOC);
        
        // Tambahkan statistik ke data dosen
        $dosen['stat_portofolio'] = $stat_portofolio['total'] ?? 0;
        $dosen['stat_komentar'] = $stat_komentar['total'] ?? 0;
        
        echo json_encode([
            'success' => true, 
            'data' => $dosen
        ]);
    } else {
        error_log("Dosen not found in database for ID: " . $dosen_id);
        echo json_encode([
            'success' => false, 
            'message' => 'Data dosen tidak ditemukan di database.',
            'debug' => ['dosen_id' => $dosen_id]
        ]);
    }
} catch (PDOException $e) {
    error_log("Database error in get_dosen.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("General error in get_dosen.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>