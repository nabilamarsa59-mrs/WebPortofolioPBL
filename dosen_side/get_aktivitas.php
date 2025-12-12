<?php
session_start();
require_once 'koneksi.php';

// Cek session
if (!isset($_SESSION['id_dosen']) && !isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// Set header JSON
header('Content-Type: application/json');

// Ambil ID dosen dari session
$dosen_id = isset($_SESSION['id_dosen']) ? $_SESSION['id_dosen'] : $_SESSION['id'];

try {
    // Ambil aktivitas terakhir (penilaian)
    $sql = "SELECT 
                p.judul,
                m.nama_lengkap as nama_mahasiswa,
                pen.nilai,
                pen.komentar,
                pen.tanggal as waktu_penilaian
            FROM penilaian pen
            JOIN projects p ON pen.id_project = p.id
            JOIN mahasiswa m ON p.id_mahasiswa = m.id
            WHERE pen.id_dosen = ?
            ORDER BY pen.tanggal DESC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $aktivitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formatted_activities = [];
    foreach ($aktivitas as $act) {
        $deskripsi = "Memberikan nilai " . $act['nilai'] . " untuk portofolio " . $act['nama_mahasiswa'];
        if ($act['komentar']) {
            $deskripsi .= " dengan komentar";
        }
        
        // Format waktu
        $waktu = date('d M Y H:i', strtotime($act['waktu_penilaian']));
        
        $formatted_activities[] = [
            'deskripsi' => $deskripsi,
            'waktu' => $waktu
        ];
    }
    
    // Jika tidak ada aktivitas, beri default
    if (empty($formatted_activities)) {
        $formatted_activities = [
            ['deskripsi' => 'Belum ada aktivitas penilaian', 'waktu' => '-']
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $formatted_activities]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>