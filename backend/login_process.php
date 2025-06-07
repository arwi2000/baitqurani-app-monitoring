<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM akun WHERE nomor_induk_santri = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['nis'] = $user['nomor_induk_santri'];
            $_SESSION['role'] = $user['role'] ?? 'siswa';
            header("Location: index.php");
            exit();
        } else {
            echo "Password salah! <a href='login.php'>Kembali</a>";
        }
    } else {
        echo "Username/NIS tidak ditemukan! <a href='login.php'>Kembali</a>";
    }
} else {
    header("Location: login.php");
    exit();
}
