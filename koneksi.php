<?php
// Menyimpan informasi host database (biasanya localhost untuk server lokal)
$host = 'localhost';

// Nama database yang akan digunakan
$db = 'db_portofolio';

// Username untuk koneksi database
$user = 'root';
// Password untuk koneksi database (kosong jika default XAMPP)
$pass = '';

// Blok try digunakan untuk menangani kemungkinan error saat koneksi database
try {

    // Membuat koneksi ke database menggunakan PDO dengan charset UTF-8
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

    // Mengatur mode error PDO agar menampilkan exception jika terjadi error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Blok catch akan dijalankan jika terjadi error saat koneksi database
} catch (PDOException $e) {

    // Menghentikan program dan menampilkan pesan error koneksi
    die("Koneksi gagal: " . $e->getMessage());
}
?>