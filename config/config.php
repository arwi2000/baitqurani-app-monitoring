<?php
// Base URL (biar gampang nanti saat hosting)
define('BASE_URL', 'http://localhost/baitqurani-app-monitoring');

// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_bqapp";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// PENTING: Set karakter set koneksi ke utf8mb4
mysqli_set_charset($conn, "utf8mb4");
