<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php"); 
    exit(); 
}

 $current_file = $_SERVER['PHP_SELF'];
if (strpos($current_file, '/dosen_side/') !== false && $_SESSION['role'] !== 'dosen') {
    header("Location: ../login.php");
    exit();
}

if (strpos($current_file, '/mahasiswa_side/') !== false && $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}
?>