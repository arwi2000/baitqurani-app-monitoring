<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../'));
include_once(BASE_DIR . '/config/config.php');

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['nis']) || $_SESSION['role'] != 'admin') {
    $_SESSION['message'] = "Harus login terlebih dahulu.";
    $_SESSION['message_type'] = "warning";
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>

    <!-- Custom CSS kamu -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <div class="sidebar">
        <img src="<?= BASE_URL ?>/assets/img/sidebar-logo.png" alt="Logo" class="sidebar-logo" />
        <h2>Admin BQ</h2>
        <hr class="separator" />
        <div class="sidebar-text">
            <a href="<?= BASE_URL ?>/admin/dashboard.php"><img src="<?= BASE_URL ?>/assets/img/dashboard.svg" class="menu-icon" /> BERANDA</a>
            <a href="<?= BASE_URL ?>/admin/santri/index.php"><img src="<?= BASE_URL ?>/assets/img/student-card.svg" class="menu-icon" /> SANTRI</a>

            <div class="tahfidz-dropdown">
                <a href="#" class="tahfidz-toggle-dropdown">
                    <img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> TAHFIDZ <span class="arrow">&#9662;</span>
                </a>
                <div class="tahfidz-menu-dropdown">
                    <a href="<?= BASE_URL ?>/admin/tahfidz/ziyadah.php"><img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> ZIYADAH</a>
                    <a href="<?= BASE_URL ?>/admin/tahfidz/murojaah.php"><img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> MUROJA'AH</a>
                    <a href="<?= BASE_URL ?>/admin/tahfidz/tasmi.php"><img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> TASMI'</a>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/admin/rekap/index.php"><img src="<?= BASE_URL ?>/assets/img/report.svg" class="menu-icon" /> REKAP</a>
        </div>
    </div>

    <div class="overlay"></div>

    <div class="main">
        <div class="header">
            <div class="header-texts">
                <a href="<?= BASE_URL ?>/login/logout.php">
                    <img src="<?= BASE_URL ?>/assets/img/logout.svg" alt="Logout" class="logout-icon">
                </a>
                <div class="header-row">
                    <img src="<?= BASE_URL ?>/assets/img/hamburger.svg" class="hamburger-icon" />
                    <h1>BQ TAHFIDZ</h1>
                </div>
            </div>
            <h3 class="header-subtitle">BERANDA</h3>
            <div class="header-main"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>

</html>