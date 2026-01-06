<?php
session_start();
require_once '../koneksi.php';

header('Content-Type: application/json');

// cek apakah user sudah login sebagai dosen
if (!isset($_SESSION['id_dosen']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// ambil ID dosen dari session
$dosen_id = $_SESSION['id_dosen'];

// query untuk mengambil 5 aktivitas penilaian terbaru oleh dosen
try {
    $sql = "SELECT 
                p.judul,
                m.nama_lengkap as nama_mahasiswa,
                pen.nilai,
                pen.komentar,
                pen.tanggal_dinilai as waktu_penilaian
            FROM penilaian pen
            JOIN projects p ON pen.id_project = p.id
            JOIN mahasiswa m ON p.id_mahasiswa = m.id
            WHERE pen.id_dosen = ?
            ORDER BY pen.tanggal_dinilai DESC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$dosen_id]);
    $aktivitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formatted_activities = [];
    // Loop setiap aktivitas untuk membuat deskripsi yang lebih ramah dibaca
    foreach ($aktivitas as $act) {
        $deskripsi = "Memberikan nilai " . $act['nilai'] . " untuk proyek \"" . $act['judul'] . "\" milik " . $act['nama_mahasiswa'];
        if ($act['komentar']) {
            $deskripsi .= " dengan komentar";
        }
        // format tanggal
        $waktu = date('d M Y H:i', strtotime($act['waktu_penilaian']));
        
        $formatted_activities[] = [
            'deskripsi' => $deskripsi,
            'waktu' => $waktu
        ];
    }
    
    if (empty($formatted_activities)) {
        $formatted_activities = [
            ['deskripsi' => 'Belum ada aktivitas penilaian', 'waktu' => '-']
        ];
    }
    // Kirim response sukses beserta data
    echo json_encode(['success' => true, 'data' => $formatted_activities]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>