<?php
// --- KONFIGURASI DATABASE ---
 $host = 'localhost';
 $username = 'root';     // Username MySQL kamu
 $password = '';         // Password MySQL kamu
 $dbname = 'db_portofolio'; // Nama database kamu (yang di foto)

// Opsi PDO biar lebih aman
 $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Mencoba membuat koneksi
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error
    die("Koneksi database gagal: " . $e->getMessage());
}
?>