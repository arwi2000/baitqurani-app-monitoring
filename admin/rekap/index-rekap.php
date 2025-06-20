<?php
session_start();

define('BASE_DIR', realpath(__DIR__ . '/../../'));
// PENTING: Pastikan config.php ini sudah membuat variabel $conn yang aktif
include_once(BASE_DIR . '/config/config.php');

// Setelah baris di atas, variabel $conn seharusnya sudah tersedia dan merupakan koneksi yang aktif.
// Jika $conn tidak terdefinisi atau koneksi gagal dari config.php, maka harus ada penanganan error di config.php itu sendiri.

// Cek apakah user sudah login dan memiliki role 'admin'
if (!isset($_SESSION['nis']) || $_SESSION['role'] !== 'admin') {
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
    <title>Rekap Data Santri & Tahfidz</title>
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
            <a href="<?= BASE_URL ?>/admin/rekap/index-rekap.php" class="active"><img src="<?= BASE_URL ?>/assets/img/report.svg" class="menu-icon" /> REKAP</a>
        </div>
    </div>

    <div class="overlay"></div>

    <div class="main">
        <div class="header">
            <div class="header-texts">
                <a href="<?= BASE_URL ?>/login/logout.php" class="logout-link">
                    <img src="<?= BASE_URL ?>/assets/img/logout.svg" alt="Logout" class="logout-icon">
                </a>
                <div class="header-row">
                    <img src="<?= BASE_URL ?>/assets/img/hamburger.svg" class="hamburger-icon" />
                    <h1>BQ TAHFIDZ</h1>
                </div>
            </div>
            <h3 class="header-subtitle">REKAPITULASI</h3>
            <div class="header-main">
                <div class="container-fluid">
                    <div class="filter-controls mb-3">
                        <div class="row g-2">
                            <div class="col-md-3 col-sm-6">
                                <label for="filterKelas" class="form-label visually-hidden">Filter Kelas</label>
                                <select class="form-select form-select-sm" id="filterKelas">
                                    <option value="">Pilih Kelas</option>
                                    <option value="Kelas 7">Kelas 7</option>
                                    <option value="Kelas 8">Kelas 8</option>
                                    <option value="Kelas 9">Kelas 9</option>
                                    <option value="Kelas 10">Kelas 10</option>
                                    <option value="Kelas 11">Kelas 11</option>
                                    <option value="Kelas 12">Kelas 12</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="filterJenisKelamin" class="form-label visually-hidden">Filter Jenis Kelamin</label>
                                <select class="form-select form-select-sm" id="filterJenisKelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="filterJenisTahfidz" class="form-label visually-hidden">Filter Jenis Tahfidz</label>
                                <select class="form-select form-select-sm" id="filterJenisTahfidz">
                                    <option value="">Pilih Jenis Tahfidz</option>
                                    <option value="Ziyadah">Ziyadah</option>
                                    <option value="Muroja'ah">Muroja'ah</option>
                                    <option value="Tasmi'">Tasmi'</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="filterTanggal" class="form-label visually-hidden">Filter Tanggal</label>
                                <input type="date" class="form-control form-control-sm" id="filterTanggal">
                            </div>
                            <div class="col-12 mt-2">
                                <button id="resetFilters" class="btn btn-secondary btn-sm w-100">Reset Filter</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <button id="exportExcelBtn" class="btn btn-success btn-sm">
                            Export Data ke Excel
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="data-gabungan" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Lengkap</th>
                                    <th>Kelas</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Tahfidz</th>
                                    <th>Jenis Tahfidz</th>
                                    <th>Halaman</th>
                                    <th>Juz</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                // Fetch all tahfidz data from database
                                // Anda mungkin ingin JOIN dengan tabel santri di sini jika kolom 'nama', 'kelas', 'jenis_kelamin' tidak ada di tabel 'tahfidz'
                                // Tapi berdasarkan kode sebelumnya, sepertinya kolom ini ada di tabel tahfidz.
                                $list = mysqli_query($conn, "SELECT * FROM tahfidz ORDER BY tanggal DESC");
                                while ($data = mysqli_fetch_array($list)) :
                                    // Konversi 'l'/'p' menjadi 'Laki-laki'/'Perempuan' untuk tampilan
                                    $jenis_kelamin_display = ($data['jenis_kelamin'] == 'l') ? 'Laki-laki' : (($data['jenis_kelamin'] == 'p') ? 'Perempuan' : htmlspecialchars($data['jenis_kelamin']));
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($data['nis']) ?></td>
                                        <td><?= htmlspecialchars($data['nama']) ?></td>
                                        <td><?= htmlspecialchars($data['kelas']) ?></td>
                                        <td><?= $jenis_kelamin_display ?></td>
                                        <td><?= htmlspecialchars($data['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($data['jenis_tahfidz']) ?></td>
                                        <td><?= htmlspecialchars($data['halaman']) ?></td>
                                        <td><?= htmlspecialchars($data['juz']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
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

    <script>
        $(document).ready(function() {
            $('#exportExcelBtn').on('click', function() {
                // Ambil nilai filter yang sedang aktif
                const filterKelas = $('#filterKelas').val();
                const filterJenisKelamin = $('#filterJenisKelamin').val();
                const filterJenisTahfidz = $('#filterJenisTahfidz').val();
                const filterTanggal = $('#filterTanggal').val();

                // Buat URL dengan parameter filter
                let exportUrl = '<?= BASE_URL ?>/admin/rekap/export_excel.php?';

                if (filterKelas) {
                    exportUrl += `kelas=${encodeURIComponent(filterKelas)}&`;
                }
                if (filterJenisKelamin) {
                    // Saat mengirim ke export_excel, kirim nilai 'l' atau 'p' jika filter aktif
                    // Asumsi: filterJenisKelamin akan bernilai 'Laki-laki' atau 'Perempuan'
                    const jenisKelaminExport = (filterJenisKelamin === 'Laki-laki') ? 'l' : (filterJenisKelamin === 'Perempuan' ? 'p' : '');
                    if (jenisKelaminExport) {
                        exportUrl += `jenis_kelamin=${encodeURIComponent(jenisKelaminExport)}&`;
                    }
                }
                if (filterJenisTahfidz) {
                    exportUrl += `jenis_tahfidz=${encodeURIComponent(filterJenisTahfidz)}&`;
                }
                if (filterTanggal) {
                    exportUrl += `tanggal=${encodeURIComponent(filterTanggal)}&`;
                }

                // Hapus '&' terakhir jika ada
                if (exportUrl.endsWith('&')) {
                    exportUrl = exportUrl.slice(0, -1);
                }

                // Arahkan browser ke URL export
                window.location.href = exportUrl;
            });
        });
    </script>
</body>

</html>