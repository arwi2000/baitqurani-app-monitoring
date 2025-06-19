<?php
session_start();

define('BASE_DIR', realpath(__DIR__ . '/../../'));
include_once(BASE_DIR . '/config/config.php');

// Redirect if user is not logged in or not an admin
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

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
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
            <a href="<?= BASE_URL ?>/admin/rekap/index-rekap.php"><img src="<?= BASE_URL ?>/assets/img/report.svg" class="menu-icon" /> REKAP</a>
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
            <h3 class="header-subtitle">DATA TAHFIDZ</h3>
            <div class="header-main">
                <div class="container-fluid">
                    <div class="d-flex align-items-center mb-3">
                        <button type="button" class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#modalTambahTahfidz">
                            + Tambah Data
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="data-tahfidz" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Jenis Tahfidz</th>
                                    <th>Halaman</th>
                                    <th>Juz</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                // Fetch all tahfidz data from database
                                $list = mysqli_query($conn, "SELECT * FROM tahfidz ORDER BY tanggal DESC");
                                while ($data = mysqli_fetch_array($list)) :
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($data['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($data['nama']) ?></td>
                                        <td><?= htmlspecialchars($data['kelas']) ?></td>
                                        <td><?= htmlspecialchars($data['jenis_kelamin']) ?></td>
                                        <td><?= htmlspecialchars($data['jenis_tahfidz']) ?></td>
                                        <td><?= htmlspecialchars($data['halaman']) ?></td>
                                        <td><?= htmlspecialchars($data['juz']) ?></td>
                                        <td>
                                            <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalUbahTahfidz<?= htmlspecialchars($data['tahfidz_id']) ?>">Ubah</a>
                                            <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusTahfidz<?= htmlspecialchars($data['tahfidz_id']) ?>">Hapus</a>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalUbahTahfidz<?= htmlspecialchars($data['tahfidz_id']) ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header text-black">
                                                    <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" alt="Person Icon" />
                                                    <h1 class="modal-title fs-4 ms-2">Form Tahfidz</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST" action="hafalan.php" enctype="multipart/form-data">
                                                    <div class="modal-body text-black">
                                                        <input type="hidden" name="thid" value="<?= htmlspecialchars($data['tahfidz_id']) ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">NIS</label>
                                                            <input type="text" class="form-control" name="thnis" value="<?= htmlspecialchars($data['nis']) ?>" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal</label>
                                                            <input type="date" class="form-control" name="thtanggal" value="<?= htmlspecialchars($data['tanggal']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama</label>
                                                            <input type="text" class="form-control" name="thnama" value="<?= htmlspecialchars($data['nama']) ?>" required placeholder="Masukkan Nama">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Kelas</label>
                                                            <select class="form-select" name="thkelas" required>
                                                                <option value="<?= htmlspecialchars($data['kelas']) ?>"><?= htmlspecialchars($data['kelas']) ?></option>
                                                                <option value="Kelas 7">Kelas 7</option>
                                                                <option value="Kelas 8">Kelas 8</option>
                                                                <option value="Kelas 9">Kelas 9</option>
                                                                <option value="Kelas 10">Kelas 10</option>
                                                                <option value="Kelas 11">Kelas 11</option>
                                                                <option value="Kelas 12">Kelas 12</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Jenis Kelamin</label>
                                                            <select class="form-select" required name="thkelamin">
                                                                <option value="<?= htmlspecialchars($data['jenis_kelamin']) ?>"><?= htmlspecialchars($data['jenis_kelamin']) == 'l' ? 'Laki-Laki' : 'Perempuan' ?></option>
                                                                <option value="Laki-laki">Laki-Laki</option>
                                                                <option value="Perempuan">Perempuan</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Jenis Tahfidz</label>
                                                            <select class="form-select" name="thtahfidz" required>
                                                                <option value="<?= htmlspecialchars($data['jenis_tahfidz']) ?>"><?= htmlspecialchars($data['jenis_tahfidz']) ?></option>
                                                                <option value="Ziyadah">Ziyadah</option>
                                                                <option value="Muroja'ah">Muroja'ah</option>
                                                                <option value="Tasmi'">Tasmi'</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Halaman</label>
                                                            <input type="text" class="form-control" name="thhalaman" value="<?= htmlspecialchars($data['halaman']) ?>" required placeholder="Masukkan Halaman">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Juz</label>
                                                            <input type="text" class="form-control" name="thjuz" value="<?= htmlspecialchars($data['juz']) ?>" required placeholder="Masukkan Juz">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary" name="hubah">Ubah</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modalHapusTahfidz<?= htmlspecialchars($data['tahfidz_id']) ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header text-black">
                                                    <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" alt="Person Icon" />
                                                    <h1 class="modal-title fs-4 ms-2">Hapus Data</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <form method="POST" action="hafalan.php" enctype="multipart/form-data">
                                                    <input type="hidden" name="thid" value="<?= htmlspecialchars($data['tahfidz_id']) ?>">
                                                    <div class="modal-body text-black">
                                                        <h5 class="text-center">
                                                            Apakah anda yakin akan menghapus data ini? <br>
                                                            <span class="text-danger"><?= htmlspecialchars($data['tanggal']) ?> - <?= htmlspecialchars($data['nama']) ?></span>
                                                        </h5>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary" name="hhapus">Hapus</button>
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

            <div class="modal fade" id="modalTambahTahfidz" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header text-black">
                            <img src="<?= BASE_URL ?>/assets/img/person-circle.svg" style="width: 24px; height: 24px;" />
                            <h1 class="modal-title fs-4 ms-2">Form Tahfidz</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="hafalan.php" enctype="multipart/form-data">
                            <div class="modal-body text-black">
                                <div class="mb-3">
                                    <label class="form-label">NIS</label>
                                    <input type="text" class="form-control" name="thnis" required placeholder="Masukkan Nomor Induk Santri">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="thtanggal" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" name="thnama" required placeholder="Masukkan Nama">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelas</label>
                                    <select class="form-select" name="thkelas" required>
                                        <option value="">Pilih Kelas</option>
                                        <option value="Kelas 7">Kelas 7</option>
                                        <option value="Kelas 8">Kelas 8</option>
                                        <option value="Kelas 9">Kelas 9</option>
                                        <option value="Kelas 10">Kelas 10</option>
                                        <option value="Kelas 11">Kelas 11</option>
                                        <option value="Kelas 12">Kelas 12</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" required name="thkelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Tahfidz</label>
                                    <select class="form-select" name="thtahfidz" required>
                                        <option value="">Pilih Jenis Tahfidz</option>
                                        <option value="Ziyadah">Ziyadah</option>
                                        <option value="Muroja'ah">Muroja'ah</option>
                                        <option value="Tasmi'">Tasmi'</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Halaman</label>
                                    <input type="text" class="form-control" name="thhalaman" placeholder="Masukkan Halaman" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Juz</label>
                                    <input type="text" class="form-control" name="thjuz" required placeholder="Masukkan Juz">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" name="hsimpan">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>

</html>