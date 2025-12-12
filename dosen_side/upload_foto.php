<?php
session_start();
require_once 'koneksi.php';

// Cek session - coba beberapa kemungkinan nama session
if (!isset($_SESSION['id_dosen']) && !isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

// Ambil ID dosen dari session
$dosen_id = isset($_SESSION['id_dosen']) ? $_SESSION['id_dosen'] : $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $file = $_FILES['foto_profil'];

    // Validasi file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya JPEG, PNG, GIF yang diperbolehkan.']);
        exit();
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal 5MB)']);
        exit();
    }
    
    // Validasi error upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (ukuran maksimal 5MB)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
        ];
        
        $error_msg = $error_messages[$file['error']] ?? 'Error upload tidak diketahui';
        echo json_encode(['success' => false, 'message' => $error_msg]);
        exit();
    }
    
    try {
        // Generate nama file unik
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_filename = 'dosen_' . $dosen_id . '_' . time() . '.' . $file_extension;
        
        // Pastikan folder uploads/dosen ada
        $upload_dir = 'uploads/dosen/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $upload_path = $upload_dir . $new_filename;
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Update database
            $sql = "UPDATE dosen SET foto_profil = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$upload_path, $dosen_id]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Foto profil berhasil diperbarui', 
                'path' => $upload_path
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload file. Pastikan folder uploads memiliki izin write.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error database: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
}
?>