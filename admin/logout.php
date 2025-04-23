<?php
// Memulai sesi untuk menggunakan variabel session
session_start();

// Menghapus semua data sesi yang sedang aktif
session_destroy();

// Mengarahkan pengguna ke halaman index.php yang ada di direktori induk
header('Location: ../index.php');

// Menghentikan eksekusi skrip untuk memastikan tidak ada kode lain yang berjalan
exit();
?>