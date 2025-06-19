<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../../'));
include_once(BASE_DIR . '/config/config.php');

// Pastikan koneksi database ($conn) sudah terdefinisi dari config.php
if (!isset($conn)) {
    // Fallback koneksi jika tidak didefinisikan di config.php
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "nama_database_anda"; // Ganti dengan nama database Anda

    $conn = mysqli_connect($server, $username, $password, $database);

    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
}

// Ensure only admins can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<script>alert('Akses ditolak!'); location.href='" . BASE_URL . "/login/login.php';</script>");
}

/**
 * Redirects with an alert message.
 * Menggunakan alert() dan location.href langsung sesuai permintaan.
 */
function redirect($msg, $location = 'tahfidz.php')
{
    // Pastikan BASE_URL sudah benar di config.php
    exit("<script>alert('$msg'); location.href='" . BASE_URL . "/admin/tahfidz/$location';</script>");
}

// Handle Add Program Data request
if (isset($_POST['hsimpan'])) {
    $nis = trim($_POST['thnis']);
    $tanggal = trim($_POST['thtanggal']);
    // Kita tidak akan menggunakan $_POST['thnama'], $_POST['thkelas'], $_POST['thkelamin'] langsung
    // untuk INSERT karena kita akan mengambilnya dari tabel users untuk konsistensi.
    // Tapi kita perlu variabel ini untuk mengisi parameter bind_param jika kolomnya masih ada di tahfidz.
    $nama = '';
    $kelas = '';
    $jeniskelamin = '';

    $jenistahfidz = trim($_POST['thtahfidz']);
    $halaman = trim($_POST['thhalaman']);
    $juz = trim($_POST['thjuz']);

    // --- Validasi NIS di tabel users SEBELUM INSERT ke tahfidz ---
    $stmt_check_nis = mysqli_prepare($conn, "SELECT nama_lengkap, kelas, jenis_kelamin FROM users WHERE nis = ?");
    if ($stmt_check_nis === false) {
        redirect("Gagal menyiapkan query cek NIS: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_check_nis, "s", $nis);
    mysqli_stmt_execute($stmt_check_nis);
    $result_check_nis = mysqli_stmt_get_result($stmt_check_nis);

    if (mysqli_num_rows($result_check_nis) == 0) {
        // NIS TIDAK DITEMUKAN di tabel users
        mysqli_stmt_close($stmt_check_nis);
        redirect("Data gagal ditambahkan! NIS tidak terdaftar! Silakan daftarkan NIS anda terlebih dahulu.");
    } else {
        // NIS DITEMUKAN, ambil data nama, kelas, jenis_kelamin dari tabel users
        $user_info = mysqli_fetch_assoc($result_check_nis);
        $nama = $user_info['nama_lengkap'];
        $kelas = $user_info['kelas'];
        $jeniskelamin = $user_info['jenis_kelamin'];
    }
    mysqli_stmt_close($stmt_check_nis);
    // --- Akhir Validasi NIS ---


    // Use prepared statement for security
    // Karena kita sudah mengambil nama, kelas, jeniskelamin dari tabel users,
    // kita akan menggunakan variabel yang sudah disiapkan tersebut.
    $stmt = mysqli_prepare($conn, "INSERT INTO tahfidz (nis, tanggal, nama, kelas, jenis_kelamin, jenis_tahfidz, halaman, juz) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        redirect("Failed to prepare add query: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssssss", $nis, $tanggal, $nama, $kelas, $jeniskelamin, $jenistahfidz, $halaman, $juz);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Data tahfidz berhasil disimpan!"); // Mengganti "Ubah data Sukses!" menjadi pesan yang lebih sesuai
    } else {
        // Error ini hanya akan muncul jika ada masalah lain dengan database (bukan karena FK)
        redirect("Error! Data tahfidz berhasil disimpan!" . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
}

// Handle Update Program Data request
if (isset($_POST['hubah'])) {
    $id = trim($_POST['thid']);
    $tanggal = trim($_POST['thtanggal']);
    // Kita perlu mendapatkan NIS dari catatan tahfidz yang akan diupdate terlebih dahulu
    // untuk kemudian mengambil info nama, kelas, jenis_kelamin terbaru dari tabel users.

    $jenistahfidz = trim($_POST['thtahfidz']);
    $halaman = trim($_POST['thhalaman']);
    $juz = trim($_POST['thjuz']);

    // --- START: Logic untuk mendapatkan NIS dari catatan tahfidz yang akan diupdate
    // Lalu, ambil nama, kelas, jenis_kelamin terbaru dari tabel users berdasarkan NIS tersebut
    $nama_santri_updated = '';
    $kelas_santri_updated = '';
    $jenis_kelamin_santri_updated = '';
    $current_nis = '';

    $stmt_get_tahfidz_nis = mysqli_prepare($conn, "SELECT nis FROM tahfidz WHERE tahfidz_id = ?");
    if ($stmt_get_tahfidz_nis === false) {
        redirect("Gagal menyiapkan query untuk mendapatkan NIS tahfidz: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_get_tahfidz_nis, "i", $id);
    mysqli_stmt_execute($stmt_get_tahfidz_nis);
    $result_tahfidz_nis = mysqli_stmt_get_result($stmt_get_tahfidz_nis);

    if ($tahfidz_data = mysqli_fetch_assoc($result_tahfidz_nis)) {
        $current_nis = $tahfidz_data['nis'];

        $stmt_get_user_info_for_update = mysqli_prepare($conn, "SELECT nama_lengkap, kelas, jenis_kelamin FROM users WHERE nis = ?");
        if ($stmt_get_user_info_for_update === false) {
            redirect("Gagal menyiapkan query info santri untuk update: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_get_user_info_for_update, "s", $current_nis);
        mysqli_stmt_execute($stmt_get_user_info_for_update);
        $result_user_info_for_update = mysqli_stmt_get_result($stmt_get_user_info_for_update);

        if ($user_info_for_update = mysqli_fetch_assoc($result_user_info_for_update)) {
            $nama_santri_updated = $user_info_for_update['nama_lengkap'];
            $kelas_santri_updated = $user_info_for_update['kelas'];
            $jenis_kelamin_santri_updated = $user_info_for_update['jenis_kelamin'];
        } else {
            // Ini seharusnya tidak terjadi jika Foreign Key berfungsi dengan baik
            // Namun, jika terjadi, berarti ada data inkonsisten di DB Anda
            redirect("NIS terkait dengan catatan tahfidz ini tidak ditemukan di daftar santri utama. Data mungkin tidak konsisten.");
        }
        mysqli_stmt_close($stmt_get_user_info_for_update);
    } else {
        redirect("Catatan tahfidz dengan ID '$id' tidak ditemukan.", 'tahfidz.php');
    }
    mysqli_stmt_close($stmt_get_tahfidz_nis);
    // --- END: Logic untuk mendapatkan NIS dan info santri terbaru untuk update

    // Query UPDATE. Kolom 'nama', 'kelas', 'jenis_kelamin' di-update dengan data terbaru dari tabel 'users'
    // berdasarkan NIS yang terkait dengan tahfidz_id tersebut.
    $stmt = mysqli_prepare($conn, "UPDATE tahfidz SET tanggal=?, nama=?, kelas=?, jenis_kelamin=?, jenis_tahfidz=?, halaman=?, juz=? WHERE tahfidz_id=?");

    if ($stmt === false) {
        redirect("Failed to prepare update query: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sssssssi", $tanggal, $nama_santri_updated, $kelas_santri_updated, $jenis_kelamin_santri_updated, $jenistahfidz, $halaman, $juz, $id);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Data tahfidz berhasil diperbarui!");
    } else {
        redirect("Error! Data tahfidz gagal diperbarui!" . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
}

// Handle Delete Program Data request
if (isset($_POST['hhapus'], $_POST['thid'])) {
    $id = trim($_POST['thid']);

    // Use prepared statement for security
    $stmt = mysqli_prepare($conn, "DELETE FROM tahfidz WHERE tahfidz_id=?");

    if ($stmt === false) {
        redirect("Failed to prepare delete query: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Data tahfidz berhasil dihapus!");
    } else {
        redirect("Error! Data tahfidz gagal dihapus!" . mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
}

// Close database connection
if (isset($conn)) {
    mysqli_close($conn);
}
