<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'tahfidz_db';

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
    
define('BASE_URL', '/baitqurani-app-monitoring/public');

}
