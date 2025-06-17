<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../../'));
include_once(BASE_DIR . '/config/config.php');

// Validasi login admin
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

    <!-- Custom CSS kamu -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/santri.css" />



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
            <h3 class="header-subtitle">DATA SANTRI</h3>
            <div class="header-main">
                <div class="container-fluid">
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        + Tambah Data
                    </button>
                
                    <!-- Tabel -->
                    <div class="table-responsive">
                        <table id="data-santri" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>NAMA</th>
                                    <th>Kelas</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $list = mysqli_query($conn, "SELECT * FROM users ORDER BY nis DESC");
                                while ($data = mysqli_fetch_array($list)) :
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $data['nis'] ?></td>
                                        <td><?= $data['nama_lengkap'] ?></td>
                                        <td><?= $data['kelas'] ?></td>
                                        <td><?= $data['jenis_kelamin'] ?></td>
                                        <td>
                                            <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalUbah<?= $no ?>">Ubah</a>
                                            <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $no ?>">Hapus</a>

                                        </td>
                                    </tr>

                                    <!-- Modal Ubah -->
                                    <div class="modal fade" id="modalUbah<?= $no ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog  modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header text-black">
                                                    <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" />
                                                    <h1 class="modal-title fs-4 ms-2">Form Data Santri</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST" action="aksi.php" enctype="multipart/form-data">
                                                    <div class="modal-body text-black">
                                                        <div class="mb-3">
                                                            <label class="form-label">NIS</label>
                                                            <input type="text" class="form-control" name="tnis" value="<?= $data['nis'] ?>" readonly required placeholder="Masukkan Nomor Induk Santri">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Lengkap</label>
                                                            <input type="text" class="form-control" name="tnama" value="<?= $data['nama_lengkap'] ?>" placeholder="Masukkan Nama Lengkap">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Kelas</label>
                                                            <select class="form-select" name="tkelas" placeholder="Pilih Kelas">
                                                                <option value="<?= $data['kelas'] ?>"><?= $data['kelas'] ?></option>
                                                                <option value="Kelas 7">Kelas 7</option>
                                                                <option value="Kelas 8">Kelas 8</option>
                                                                <option value="Kelas 9">Kelas 9</option>
                                                                <option value="Kelas 10">Kelas 10</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Jenis Kelamin</label>
                                                            <select class="form-select" name="tjeniskelamin" placeholder="Pilih Jenis Kelamin">
                                                                <option value="<?= $data['jenis_kelamin'] ?>"><?= $data['jenis_kelamin'] ?></option>
                                                                <option value="L">Laki-laki</option>
                                                                <option value="P">Perempuan</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Role</label>
                                                            <select class="form-select" name="trole" placeholder="Pilih Role">
                                                                <option value="<?= $data['role'] ?>"><?= $data['role'] ?></option>
                                                                <option value="admin">Admin</option>
                                                                <option value="user">User</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Password</label>
                                                            <input type="text" class="form-control" name="tpassword" value="<?= $data['password'] ?>" placeholder="Masukkan Password">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Upload Foto</label>
                                                            <input type="file" class="form-control" name="tfoto" value="<?= $data['foto'] ?>" accept="image" placeholder="Masukkan Foto">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary" name="bubah">Ubah</button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Ubah -->

                                    <!-- Modal Hapus -->
                                    <div class="modal fade" id="modalHapus<?= $no ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog  modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header text-black">
                                                    <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" />
                                                    <h1 class="modal-title fs-4 ms-2">Hapus Data</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST" action="aksi.php" enctype="multipart/form-data">
                                                    <input type="hidden" name="nis" value="<?= $data['nis'] ?>">

                                                    <div class="modal-body text-black">
                                                        <h5 class="text-center">
                                                            Apakah anda yakin akan menghapus data ini? <br>
                                                            <span class="text-danger"><?= $data['nis'] ?> - <?= $data['nama_lengkap'] ?></span>
                                                        </h5>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary" name="bhapus">Hapus</button>
                                                    </div>
                                                </form>


                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal Hapus -->

                                <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Modal Tambah -->
            <div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog  modal-lg">
                    <div class="modal-content">
                        <div class="modal-header text-black">
                            <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" />
                            <h1 class="modal-title fs-4 ms-2">Form Data Santri</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="aksi.php" enctype="multipart/form-data">
                            <div class="modal-body text-black">
                                <div class="mb-3">
                                    <label class="form-label">NIS</label>
                                    <input type="text" class="form-control" name="tnis" required placeholder="Masukkan Nomor Induk Santri">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="tnama" required placeholder="Masukkan Nama Lengkap">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelas</label>
                                    <select class="form-select" name="tkelas" required placeholder="Pilih Kelas">
                                        <option value=""></option>
                                        <option value="Admin">Admin</option>
                                        <option value="Kelas 7">Kelas 7</option>
                                        <option value="Kelas 8">Kelas 8</option>
                                        <option value="Kelas 9">Kelas 9</option>
                                        <option value="Kelas 10">Kelas 10</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="tjeniskelamin" required placeholder="Pilih Jenis Kelamin">
                                        <option value=""></option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" name="trole" required placeholder="Pilih Role">
                                        <option value=""></option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" name="tpassword" required placeholder="Masukkan Password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Foto</label>
                                    <input type="file" class="form-control" name="tfoto" accept="image" required placeholder="Masukkan Foto">
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
            <!-- Modal Tambah -->
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


    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>


</html>