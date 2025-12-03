<?php
<<<<<<< HEAD
 $host = "localhost";
 $user = "root";
 $pass = "";
 $dbname = "db_portfolio";
=======
// Konfigurasi koneksi ke database
 $host = 'localhost';      // atau 127.0.0.1
 $dbname = 'db_portofolio'; // Nama database yang Anda buat
 $username = 'root';        // Username database MySQL Anda (default: root)
 $password = '';            // Password database MySQL Anda (default: kosong untuk XAMPP)
>>>>>>> c2fae3ab9e64d9fc249bc19b89dc7ed455feef29

// Buat DSN (Data Source Name)
 $dsn = "mysql:host=$host;dbname=$dbname";

// Opsi PDO
 $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan error exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch mode default menjadi associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Menggunakan prepared statement asli
];

// Mencoba membuat koneksi
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error dan hentikan skrip
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>