<?php
// Memulai session agar session yang aktif bisa diakses
session_start();

// Menghapus seluruh data session (logout user)
session_destroy();

// Mengarahkan user kembali ke halaman login setelah logout
header("Location: login.php");

// Menghentikan eksekusi script agar redirect berjalan sempurna
exit();
?>
