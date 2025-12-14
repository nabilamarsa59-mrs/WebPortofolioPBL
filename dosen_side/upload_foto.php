<?php
session_start();
require_once 'koneksi.php';

// Cek session dosen
if (!isset($_SESSION['id_dosen'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

$dosen_id = $_SESSION['id_dosen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $file = $_FILES['foto_profil'];
    
    // Validasi file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya JPEG, PNG, GIF yang diperbolehkan']);
        exit();
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)']);
        exit();
    }
    
    try {
        // Generate nama file unik
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'dosen_' . $dosen_id . '_' . time() . '.' . $file_extension;
        $upload_path = '../uploads/dosen/' . $new_filename; // Tambahkan ../
        
        // Buat direktori jika belum ada
        $upload_dir = '../uploads/dosen/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update database
            $relative_path = 'uploads/dosen/' . $new_filename;
            $sql = "UPDATE dosen SET foto_profil = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$relative_path, $dosen_id]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Foto profil berhasil diperbarui', 
                'path' => $relative_path
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error database: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
}
?>