<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['id_dosen'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $dosen_id = $_SESSION['id_dosen'];
    $file = $_FILES['foto_profil'];
    
    // Validasi
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Hanya file gambar yang diizinkan']);
        exit();
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
        exit();
    }
    
    try {
        // Generate nama file
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'dosen_' . $dosen_id . '_' . time() . '.' . $ext;
        $upload_dir = '../uploads/dosen/';
        $upload_path = $upload_dir . $filename;
        
        // Buat folder jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update database
            $relative_path = 'uploads/dosen/' . $filename;
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
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
}
?>