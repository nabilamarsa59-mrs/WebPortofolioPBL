<?php
session_start();
require_once '../koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login" || $_SESSION['level'] != "dosen") {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['foto_profil'];
    
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
        $sql_dosen = "SELECT d.id, d.nidn, d.foto_profil
                      FROM users u
                      JOIN dosen d ON u.id_dosen = d.id
                      WHERE u.id = ?";
        $stmt_dosen = $pdo->prepare($sql_dosen);
        $stmt_dosen->execute([$user_id]);
        $dosen = $stmt_dosen->fetch(PDO::FETCH_ASSOC);
        
        if (!$dosen) {
            echo json_encode(['success' => false, 'message' => 'Data dosen tidak ditemukan']);
            exit();
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profil_dosen_' . $dosen['nidn'] . '_' . uniqid() . '.' . $ext;
        
        $upload_dir = '../uploads/';
        $upload_path = $upload_dir . $filename;
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            if (!empty($dosen['foto_profil']) && 
                $dosen['foto_profil'] !== 'default-avatar.jpg' && 
                file_exists($upload_dir . $dosen['foto_profil'])) {
                unlink($upload_dir . $dosen['foto_profil']);
            }
            
            $sql_update = "UPDATE dosen SET foto_profil = ? WHERE id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$filename, $dosen['id']]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Foto profil berhasil diperbarui',
                'path' => $upload_path
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload file. Periksa permission folder uploads/']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error database: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada file yang diupload']);
}
?>