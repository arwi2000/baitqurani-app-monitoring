<?php
session_start();
define('BASE_DIR', realpath(__DIR__));
include_once(BASE_DIR . '/config/config.php');

// Jika belum login, arahkan ke halaman login
if (!isset($_SESSION['nis']) || !isset($_SESSION['role'])) {
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// Arahkan berdasarkan role
switch ($_SESSION['role']) {
    case 'admin':
        header("Location: " . BASE_URL . "/admin/dashboard.php");
        break;
    case 'santri':
        header("Location: " . BASE_URL . "/santri/profil.php");
        break;
    default:
        // Role tidak dikenal, arahkan ke login dengan pesan error
        $_SESSION['message'] = "Akses tidak valid.";
        $_SESSION['message_type'] = "danger";
        header("Location: " . BASE_URL . "/login/login.php");
        break;
}
exit();
?>
