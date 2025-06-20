<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../'));
include_once(BASE_DIR . '/config/config.php');

if (!isset($_SESSION['nis']) || $_SESSION['role'] != 'user') {
  $_SESSION['message'] = "Anda harus login terlebih dahulu.";
  $_SESSION['message_type'] = "warning";
  header("Location: " . BASE_URL . "/login/login.php");
  exit();
}

$nis_logged_in = $_SESSION['nis'];
$nama_santri = "Nama Santri Tidak Ditemukan";
$kelas_santri = "Kelas Tidak Ditemukan";
$jenis_kelamin_santri = "Jenis Kelamin Tidak Ditemukan";
$foto_profil_path = BASE_URL . "/assets/img/profil.svg";

$stmt_user_profile = mysqli_prepare($conn, "SELECT nama_lengkap, kelas, jenis_kelamin, foto FROM users WHERE nis = ? LIMIT 1");
if ($stmt_user_profile === false) {
  die("Gagal menyiapkan query profil santri dari users: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt_user_profile, "s", $nis_logged_in);
mysqli_stmt_execute($stmt_user_profile);
$result_user_profile = mysqli_stmt_get_result($stmt_user_profile);

if ($user_profile_data = mysqli_fetch_assoc($result_user_profile)) {
  $nama_santri = htmlspecialchars($user_profile_data['nama_lengkap']);
  $kelas_santri = htmlspecialchars($user_profile_data['kelas']);
  $jenis_kelamin_santri = htmlspecialchars($user_profile_data['jenis_kelamin']);

  $foto_nama_file = $user_profile_data['foto'];
  $folder_foto_fisik = BASE_DIR . '/uploads/foto-santri/';
  $folder_foto_url = BASE_URL . '/uploads/foto-santri/';
  if (!empty($foto_nama_file) && file_exists($folder_foto_fisik . $foto_nama_file)) {
    $foto_profil_path = $folder_foto_url . htmlspecialchars($foto_nama_file);
  }
} else {
  $_SESSION['message'] = "Data profil Anda tidak ditemukan di sistem. Silakan hubungi admin.";
  $_SESSION['message_type'] = "danger";
  header("Location: " . BASE_URL . "/login/logout.php");
  exit();
}
mysqli_stmt_close($stmt_user_profile);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Santri</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/profil.css" />
</head>

<body>
  <div class="main">
    <div class="header">
      <div class="header-texts">
        <a href="<?= BASE_URL ?>/login/logout.php">
          <img src="<?= BASE_URL ?>/assets/img/logout.svg" alt="Logout" class="logout-icon">
        </a>
        <div class="header-row">
          <img src="<?= BASE_URL ?>/assets/img/profil-logo.png" alt="Logo BQ Tahfidz" class="bq-tahfidsz-logo">
        </div>
        <div class="profil-santri-container">
          <img src="<?= $foto_profil_path ?>" alt="Foto Profil Santri" class="profil-foto">
          <div class="profil-details">
            <h3><?= $nama_santri ?></h3>
            <p>NIS: <?= htmlspecialchars($nis_logged_in) ?></p>
            <p>Kelas: <?= $kelas_santri ?></p>
            <p>Jenis Kelamin: <?= $jenis_kelamin_santri ?></p>
          </div>
        </div>
      </div>
      <div class="header-main">
        <div class="container-fluid">
          <div class="table-responsive">
            <table id="data-profil" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Jenis Tahfidz</th>
                  <th>Halaman</th>
                  <th>Juz</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                $ada_data = false;

                $stmt_tahfidz_data = mysqli_prepare($conn, "SELECT tanggal, jenis_tahfidz, halaman, juz FROM tahfidz WHERE nis = ? ORDER BY tahfidz_id DESC");
                if ($stmt_tahfidz_data === false) {
                  die("Gagal menyiapkan query data tahfidz: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($stmt_tahfidz_data, "s", $nis_logged_in);
                mysqli_stmt_execute($stmt_tahfidz_data);
                $result_tahfidz_data = mysqli_stmt_get_result($stmt_tahfidz_data);

                if (mysqli_num_rows($result_tahfidz_data) > 0) {
                  $ada_data = true;
                  while ($data = mysqli_fetch_array($result_tahfidz_data)) :
                ?>
                    <tr>
                      <td><?= $no++ ?></td>
                      <td><?= htmlspecialchars($data['tanggal']) ?></td>
                      <td><?= htmlspecialchars($data['jenis_tahfidz']) ?></td>
                      <td><?= htmlspecialchars($data['halaman']) ?></td>
                      <td><?= htmlspecialchars($data['juz']) ?></td>
                    </tr>
                <?php endwhile;
                }
                mysqli_stmt_close($stmt_tahfidz_data);
                mysqli_close($conn);
                ?>
              </tbody>
            </table>
          </div>

          <?php if (!$ada_data): ?>
            <div class="alert alert-warning text-center mt-3 fw-bold fs-5">
              Tabel Tahfidz Masih Kosong atau Belum Diisi.
            </div>
          <?php endif; ?>

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
  <script src="<?= BASE_URL ?>/assets/js/profil.js"></script>
  <script>
    $.fn.dataTable.ext.errMode = 'none';
    $('#data-profil').on('error.dt', function(e, settings, techNote, message) {
      console.warn("DataTables error suppressed: ", message);
    });

    $(document).ready(function() {
      $('#data-profil').DataTable({
        paging: false,
        autoWidth: false,
        responsive: true,
        language: {
          search: "Cari:",
          zeroRecords: "Data tidak ditemukan",
          emptyTable: "Tidak ada data dalam tabel",
        },
        columnDefs: [{
          targets: '_all',
          className: 'text-center'
        }],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
          '<"row"<"col-sm-12"tr>>' +
          '<"row"<"col-sm-12 col-md-5"i>>'
      });
    });
  </script>
</body>

</html>