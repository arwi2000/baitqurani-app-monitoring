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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Santri</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/santri.css" />
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="<?= BASE_URL ?>/assets/img/sidebar-logo.png" alt="Logo" class="sidebar-logo" />
        <h2>Admin BQ</h2>

        <hr class="separator" />

        <div class="sidebar-text">
            <a href="<?= BASE_URL ?>/admin/dashboard.php">
                <img src="<?= BASE_URL ?>/assets/img/dashboard.svg" class="menu-icon" /> BERANDA
            </a>
            <a href="<?= BASE_URL ?>/admin/santri/santri.php">
                <img src="<?= BASE_URL ?>/assets/img/student-card.svg" class="menu-icon" /> SANTRI
            </a>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle">
                    <img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> TAHFIDZ
                </a>
                <div class="dropdown-menu">
                    <a href="<?= BASE_URL ?>/admin/tahfidz/ziyadah.php">
                        <img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> ZIADAH
                    </a>
                    <a href="<?= BASE_URL ?>/admin/tahfidz/murojaah.php">
                        <img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> MUROJA'AH
                    </a>
                    <a href="<?= BASE_URL ?>/admin/tahfidz/tasmi.php">
                        <img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" /> TASMI'
                    </a>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/admin/rekap/index.php">
                <img src="<?= BASE_URL ?>/assets/img/report.svg" class="menu-icon" /> REKAP
            </a>
        </div>
    </div>

    <div class="overlay"></div>

    <div class="main">
        <div class="header">
            <div class="header-texts">
                <a href="<?= BASE_URL ?>/login/logout.php">
                    <img src="<?= BASE_URL ?>/assets/img/logout.svg" alt="Logout" class="logout-icon" />
                </a>

                <div class="header-row">
                    <img src="<?= BASE_URL ?>/assets/img/hamburger.svg" class="hamburger-icon" />
                    <h1>BQ TAHFIDZ</h1>
                </div>
            </div>

            <h3 class="header-subtitle">BERANDA</h3>
            <div class="header-main">
                <div class="container-fluid">
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        + Tambah Data
                    </button>

                </div>
            </div>

            <!-- Modal Tambah -->
            <div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog  modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" />
                            <h1 class="modal-title fs-4 ms-2">Form Data Santri</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="crud-santri.php" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Upload File PDF</label>
                                    <input type="file" class="form-control" name="filepdf" accept=".pdf" required>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" name="bsimpan">Simpan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JS dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#data-santri').DataTable();
        });
    </script>

</body>

</html>