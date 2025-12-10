<?php
session_start();
require_once 'koneksi.php';

// PERBAIKAN: Sesuaikan nama variabel session
if (!isset($_SESSION['id_dosen'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// PERBAIKAN: Sesuaikan nama variabel session
 $dosen_id = $_SESSION['id_dosen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {

    // Validasi file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan']);
        exit();
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar']);
        exit();
    }
    
    try {
        // Generate nama file unik
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'dosen_' . $dosen_id . '_' . time() . '.' . $file_extension;
        $upload_path = 'uploads/dosen/' . $new_filename;
        
        // Buat direktori jika belum ada
        if (!is_dir('uploads/dosen/')) {
            mkdir('uploads/dosen/', 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update database
            $sql = "UPDATE dosen SET foto_profil = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$upload_path, $dosen_id]);
            
            echo json_encode(['success' => true, 'message' => 'Foto profil berhasil diperbarui', 'path' => $upload_path]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
}
?>