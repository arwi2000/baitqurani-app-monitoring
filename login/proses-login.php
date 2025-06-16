<?php
session_start();
include_once('../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nis = trim($_POST['nis']);  
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE nis = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $nis);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($data = mysqli_fetch_assoc($result)) {
            // Verifikasi password (sementara masih tanpa hash)
            if ($password === $data['password']) {
                $_SESSION['nis'] = $data['nis'];
                $_SESSION['role'] = $data['role'];

                // Berikan pesan sukses login
                $_SESSION['message'] = "Berhasil login sebagai " . ucfirst($data['role']);
                $_SESSION['message_type'] = "success";

                if ($data['role'] == 'admin') {
                    header("Location: " . BASE_URL . "/admin/dashboard.php");
                    exit();
                } elseif ($data['role'] == 'user') {
                    header("Location: " . BASE_URL . "/santri/profil.php");
                    exit();
                } else {
                    // Role tidak dikenali
                    $_SESSION['message'] = "Role tidak dikenali.";
                    $_SESSION['message_type'] = "warning";
                    header("Location: login.php");
                    exit();
                }
            } else {
                // Password salah
                $_SESSION['message'] = "Password salah!";
                $_SESSION['message_type'] = "danger";
                header("Location: login.php");
                exit();
            }
        } else {
            // User tidak ditemukan
            $_SESSION['message'] = "NIS tidak ditemukan!";
            $_SESSION['message_type'] = "danger";
            header("Location: login.php");
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "Terjadi kesalahan pada database.";
        $_SESSION['message_type'] = "danger";
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Permintaan tidak valid.";
    $_SESSION['message_type'] = "warning";
    header("Location: login.php");
    exit();
}
?>
