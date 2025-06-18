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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

    <!-- DataTables Responsive Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />

</head>

<body>
    <div class="sidebar">
        <img src="<?= BASE_URL ?>/assets/img/sidebar-logo.png" alt="Logo" class="sidebar-logo" />
        <h2>Admin BQ</h2>
        <hr class="separator" />
        <div class="sidebar-text">
            <a href="<?= BASE_URL ?>/admin/dashboard.php"><img src="<?= BASE_URL ?>/assets/img/dashboard.svg" class="menu-icon" />BERANDA</a>
            <a href="<?= BASE_URL ?>/admin/santri/index.php"><img src="<?= BASE_URL ?>/assets/img/student-card.svg" class="menu-icon" />SANTRI</a>
            <a href="<?= BASE_URL ?>/admin/tahfidz/tahfidz.php"><img src="<?= BASE_URL ?>/assets/img/quran-02.svg" class="menu-icon" />TAHFIDZ</a>
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
            <h3 class="header-subtitle">DATA PROGRAM</h3>
            <div class="header-main">
                <div class="container-fluid">
                    <div class="d-flex align-items-center mb-3">
                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#modalTambahProgram">
                            + Tambah Data
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="data-program" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Program</th>
                                    <th>Kelas</th>
                                    <th>Semester 1</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $list = mysqli_query($conn, "SELECT * FROM program_tahfidz ORDER BY program DESC");
                                while ($data = mysqli_fetch_array($list)) :
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $data['program'] ?></td>
                                        <td><?= $data['kelas'] ?></td>
                                        <td><?= $data['semester_1'] ?></td>
                                        <td><?= $data['semester_2'] ?></td>
                                        <td>
                                            <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalUbahProgram<?= htmlspecialchars($data['program_tahfidz_id']) ?>">Ubah</a>
                                            <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusProgram<?= htmlspecialchars($data['program_tahfidz_id']) ?>">Hapus</a>

                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalUbahProgram<?= htmlspecialchars($data['program_tahfidz_id']) ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header text-black">
                                                    <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" alt="Person Icon" />
                                                    <h1 class="modal-title fs-4 ms-2">Form Program Tahfidz</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST" action="program.php" enctype="multipart/form-data">
                                                    <div class="modal-body text-black">
                                                        <input type="hidden" name="tpid" value="<?= htmlspecialchars($data['program_tahfidz_id']) ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Program</label>
                                                            <input type="text" class="form-control" name="tpprogram" value="<?= htmlspecialchars($data['program']) ?>" placeholder="Masukkan Program" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Kelas</label>
                                                            <input type="text" class="form-control" name="tpkelas" value="<?= htmlspecialchars($data['kelas']) ?>" placeholder="Masukkan Kelas" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Semester 1</label>
                                                            <input type="text" class="form-control" name="tpsemester1" value="<?= htmlspecialchars($data['semester_1']) ?>" placeholder="Masukkan Program Semester 1" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Semester 2</label>
                                                            <input type="text" class="form-control" name="tpsemester2" value="<?= htmlspecialchars($data['semester_2']) ?>" placeholder="Masukkan Program Semester 2" required>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary" name="pubah">Ubah</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modalHapusProgram<?= htmlspecialchars($data['program_tahfidz_id']) ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header text-black">
                                                    <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" alt="Person Icon" />
                                                    <h1 class="modal-title fs-4 ms-2">Hapus Data</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST" action="program.php" enctype="multipart/form-data">
                                                    <input type="hidden" name="tpid" value="<?= htmlspecialchars($data['program_tahfidz_id']) ?>">

                                                    <div class="modal-body text-black">
                                                        <h5 class="text-center">
                                                            Apakah anda yakin akan menghapus data ini? <br>
                                                            <span class="text-danger"><?= htmlspecialchars($data['program']) ?> - <?= htmlspecialchars($data['kelas']) ?></span>
                                                        </h5>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary" name="phapus">Hapus</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="modal fade" id="modalTambahProgram" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header text-black">
                            <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" />
                            <h1 class="modal-title fs-4 ms-2">Form Program Tahfidz</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="program.php" enctype="multipart/form-data">
                            <div class="modal-body text-black">
                                <div class="mb-3">
                                    <label class="form-label">Program</label>
                                    <input type="text" class="form-control" name="tpprogram" required placeholder="Masukkan Program">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelas</label>
                                    <select class="form-select" name="tpkelas" required>
                                        <option value=""></option>
                                        <option value="Kelas 7">Kelas 7</option>
                                        <option value="Kelas 8">Kelas 8</option>
                                        <option value="Kelas 9">Kelas 9</option>
                                        <option value="Kelas 10">Kelas 10</option>
                                        <option value="Kelas 11">Kelas 11</option>
                                        <option value="Kelas 12">Kelas 12</option>
                                        <option value="PK">PK</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Semester 1</label>
                                    <input type="text" class="form-control" name="tpsemester1" required placeholder="Masukkan Program Semester 1">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Semester 2</label>
                                    <input type="text" class="form-control" name="tpsemester2" required placeholder="Masukkan Program Semester 2">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" name="psimpan">Simpan</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <!-- DataTables Responsive JS -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>


    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>

</html>