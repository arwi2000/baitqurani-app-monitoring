<?php
// config.php

$host = "localhost";       // server database biasanya localhost
$user = "root";            // username database, sesuaikan dengan server kamu
$password = "";            // password database, kosong jika belum di-set
$database = "db_bqapp";    // nama database kamu

// Membuat koneksi ke MySQL
$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
