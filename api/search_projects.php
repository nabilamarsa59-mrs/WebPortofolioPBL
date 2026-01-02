<?php
// api/search_projects.php
header('Content-Type: application/json');
require_once '../koneksi.php';

// Get parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';
$jurusan = isset($_GET['jurusan']) ? trim($_GET['jurusan']) : '';

try {
    // Build base query for ALL projects (not just logged-in user's projects)
    $sql = "SELECT
                p.id,
                p.judul,
                p.deskripsi,
                k.nama_kategori,
                p.gambar,
                p.link_demo,
                p.link_source,
                p.tanggal,
                p.id_mahasiswa,
                m.nama_lengkap,
                m.nim,
                m.jurusan,
                m.foto_profil as mahasiswa_foto
            FROM projects p
            LEFT JOIN kategori_proyek k ON p.kategori = k.id
            LEFT JOIN mahasiswa m ON p.id_mahasiswa = m.id
            WHERE 1=1";

    $params = [];

    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (p.judul LIKE ? OR p.deskripsi LIKE ? OR m.nama_lengkap LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    // Add category filter
    if (!empty($kategori) && $kategori !== 'all') {
        $sql .= " AND k.nama_kategori = ?";
        $params[] = $kategori;
    }

    // Add jurusan filter - INI YANG DIPERBAIKI
    if (!empty($jurusan) && $jurusan !== 'all') {
        $sql .= " AND m.jurusan = ?";
        $params[] = $jurusan;
    }

    // Order by date desc
    $sql .= " ORDER BY p.tanggal DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $projects,
        'count' => count($projects),
        'filters' => [
            'search' => $search,
            'kategori' => $kategori,
            'jurusan' => $jurusan
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>