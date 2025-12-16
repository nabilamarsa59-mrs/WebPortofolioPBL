<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    die("Anda belum login.");
}

echo "<h1>TES UPLOAD FILE</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>PROSES UPLOAD DIMULAI</h2>";

    if (isset($_FILES['gambar'])) {
        echo "<p>File Name: " . $_FILES['gambar']['name'] . "<br>";
        echo "<p>File Type: " . $_FILES['gambar']['type'] . "<br>";
        echo "<p>File Size: " . $_FILES['gambar']['size'] . "<br>";
        echo "<p>Temp Name: " . $_FILES['gambar']['tmp_name'] . "<br>";
        echo "<p>Error Code: " . $_FILES['gambar']['error'] . "<br>";

        if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = 'tes_' . uniqid() . '.' . $file_ext;
            $upload_path = '../uploads/' . $new_file_name;

            echo "<p><strong>Path tujuan:</strong> " . $upload_path . "<br>";

            if (move_uploaded_file($file_tmp, $upload_path)) {
                echo "<p style='color:green;'><strong>SUKSES!</strong> File berhasil dipindah ke: " . $upload_path . "</p>";
                echo "<p><strong>Gambar:</strong> <img src='/WebPortofolioPBL/uploads/" . $new_file_name . "' width='200' style='border:1px solid #ccc;'></p>";
            } else {
                echo "<p style='color:red;'><strong>GAGAL!</strong> move_uploaded_file() gagal.</p>";
            }
        } else {
            echo "<p style='color:orange;'>Tidak ada file yang diupload atau ada error.</p>";
        }
    }
}

echo "<hr><h2>ISI FOLDER UPLOADS?</h2>";

 $folder_path = '../uploads';
if (is_dir($folder_path)) {
    echo "<p style='color:green;'>Folder 'uploads' DITEMUKAN.</p>";
    if ($handle = opendir($folder_path)) {
        echo "<ul>";
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                echo "<li>" . $entry . "</li>";
            }
        }
        closedir($handle);
        echo "</ul>";
    } else {
        echo "<p style='color:red;'>Folder 'uploads' TIDAK DITEMUKAN atau TIDAK BISA DIBACA.</p>";
    }
} else {
    echo "<p style='color:red;'>Path ke folder 'uploads' salah.</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tes Upload</title>
</head>
<body>
    <form action="tes_upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="gambar" accept="image/*" required>
        <button type="submit">Upload & Tes</button>
    </form>
</body>
</html>