<?php
// get_dosen.php - DIUPDATE SESUAI STRUKTUR TABEL
session_start();

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Matikan error reporting untuk production, aktifkan untuk debugging
// error_reporting(0); // Untuk production
error_reporting(E_ALL); // Untuk debugging
ini_set('display_errors', 0);

// Include koneksi dengan path yang benar
require_once __DIR__ . '/../koneksi.php';

// Cek koneksi
if (!isset($pdo) || !($pdo instanceof PDO)) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database tidak valid']);
    exit();
}

// Fungsi untuk mendapatkan ID dosen dari session
function getDosenIdFromSession() {
    // Prioritas pengambilan ID dosen dari session
    $session_keys = ['id_dosen', 'dosen_id', 'id', 'user_id'];
    
    foreach ($session_keys as $key) {
        if (isset($_SESSION[$key]) && !empty($_SESSION[$key])) {
            return intval($_SESSION[$key]);
        }
    }
    
    return null;
}

// Ambil ID dosen
$dosen_id = getDosenIdFromSession();

// Untuk development/testing saja (HAPUS di production)
// $dosen_id = 1; // Uncomment untuk testing tanpa login

if (!$dosen_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'Anda belum login atau session tidak valid',
        'redirect' => '/login.php' // Opsional: tambahkan URL redirect
    ]);
    exit();
}

try {
    // **SESUAI STRUKTUR TABEL DOSEN YANG ANDA MILIKI:**
    // id, nama_lengkap, email, jabatan, nidn, bidang_keahlian, bio, foto_profil, created_at, updated_at
    
    $sql = "SELECT 
                id, 
                nama_lengkap, 
                email, 
                jabatan,                    -- Kolom jabatan sudah ada di tabel
                nidn, 
                bidang_keahlian,
                bio,
                foto_profil,
                DATE_FORMAT(created_at, '%d-%m-%Y') as tanggal_daftar,
                DATE_FORMAT(updated_at, '%d-%m-%Y %H:%i') as terakhir_diupdate
            FROM dosen 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $dosen_id]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dosen) {
        // **STATISTIK PORTFOLIO** - Sesuaikan dengan tabel Anda
        // Asumsi Anda memiliki tabel 'penilaian' atau 'portofolio'
        
        // 1. Total portofolio/proyek
        $sql_portofolio = "SELECT COUNT(*) as total FROM penilaian WHERE id_dosen = :id";
        $stmt_portofolio = $pdo->prepare($sql_portofolio);
        $stmt_portofolio->execute([':id' => $dosen_id]);
        $stat_portofolio = $stmt_portofolio->fetch(PDO::FETCH_ASSOC);
        
        // 2. Komentar/ulasan
        $sql_komentar = "SELECT COUNT(*) as total FROM penilaian 
                        WHERE id_dosen = :id AND komentar IS NOT NULL AND komentar != ''";
        $stmt_komentar = $pdo->prepare($sql_komentar);
        $stmt_komentar->execute([':id' => $dosen_id]);
        $stat_komentar = $stmt_komentar->fetch(PDO::FETCH_ASSOC);
        
        // 3. Nilai rata-rata (jika ada kolom nilai)
        $sql_nilai = "SELECT AVG(nilai) as rata_rata FROM penilaian WHERE id_dosen = :id";
        $stmt_nilai = $pdo->prepare($sql_nilai);
        $stmt_nilai->execute([':id' => $dosen_id]);
        $stat_nilai = $stmt_nilai->fetch(PDO::FETCH_ASSOC);
        
        // Tambahkan statistik ke data dosen
        $dosen['statistik'] = [
            'total_portofolio' => $stat_portofolio['total'] ?? 0,
            'total_komentar' => $stat_komentar['total'] ?? 0,
            'rata_rata_nilai' => round($stat_nilai['rata_rata'] ?? 0, 2)
        ];
        
        // Handle foto profil jika kosong
        if (empty($dosen['foto_profil'])) {
            $dosen['foto_profil'] = 'default-avatar.jpg'; // Ganti dengan default image Anda
        }
        
        // Response sukses
        $response = [
            'success' => true,
            'data' => $dosen
        ];
        
        // Tambahkan debug info hanya di development
        if (isset($_GET['debug']) && $_GET['debug'] == 1) {
            $response['debug'] = [
                'session_id' => $dosen_id,
                'query_executed' => true
            ];
        }
        
        echo json_encode($response);
        
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Data dosen tidak ditemukan',
            'suggestion' => 'Periksa ID dosen di database'
        ]);
    }
    
} catch (PDOException $e) {
    // Log error untuk debugging
    error_log("Database Error [get_dosen.php]: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.'
        // 'debug_message' => $e->getMessage() // Hanya untuk development
    ]);
}
?>