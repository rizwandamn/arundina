<?php
session_start(); // Memulai sesi

// Menghapus semua variabel sesi
$_SESSION = [];

// Menghancurkan sesi
session_destroy();

// Mengarahkan pengguna kembali ke halaman login setelah logout
header("Location: login.php");
exit();
?>
