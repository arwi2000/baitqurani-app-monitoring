<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../'));
include_once(BASE_DIR . '/config/config.php'); // Pastikan ini mengarah ke config.php yang sudah dimodifikasi

// Alihkan non-admin ke halaman login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<script>alert('Akses ditolak.'); location.href='" . BASE_URL . "/login/login.php';</script>");
}

// Fungsi helper untuk redirect dengan alert
// Menambah parameter $location agar bisa dialihkan ke halaman yang berbeda jika perlu
function redirect($msg, $location = 'dashboard.php')
{
    exit("<script>alert('$msg'); location.href='$location';</script>");
}

// ========== TAMBAH DATA PROGRAM ==========
if (isset($_POST['psimpan'])) {
    // Tidak perlu lagi fungsi s() karena kita menggunakan Prepared Statements
    // Gunakan trim() untuk membersihkan spasi awal/akhir
    $program = trim($_POST['tpprogram']);
    $kelas = trim($_POST['tpkelas']); // Pastikan ini sesuai dengan nama input form Anda
    $semester1 = trim($_POST['tpsemester1']);
    $semester2 = trim($_POST['tpsemester2']);

    // Siapkan Prepared Statement
    $stmt = mysqli_prepare($conn, "INSERT INTO program_tahfidz (program, kelas, semester_1, semester_2) VALUES (?, ?, ?, ?)");

    // Cek apakah statement berhasil disiapkan
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query tambah: " . mysqli_error($conn));
    }

    // Bind parameter
    // 'ssss' karena semua input adalah string
    mysqli_stmt_bind_param($stmt, "ssss", $program, $kelas, $semester1, $semester2);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        redirect("Simpan data Sukses!");
    } else {
        redirect("Simpan data Gagal! Error: " . mysqli_error($conn)); // Tampilkan error untuk debugging
    }

    // Tutup statement
    mysqli_stmt_close($stmt);
}

// ========== UBAH DATA PROGRAM ==========
if (isset($_POST['pubah'])) {
    // Tidak perlu lagi fungsi s()
    $id = trim($_POST['tpid']);
    $program = trim($_POST['tpprogram']);
    $kelas = trim($_POST['tpkelas']);
    $semester1 = trim($_POST['tpsemester1']);
    $semester2 = trim($_POST['tpsemester2']);

    // Siapkan Prepared Statement
    $stmt = mysqli_prepare($conn, "UPDATE program_tahfidz SET program=?, kelas=?, semester_1=?, semester_2=? WHERE program_tahfidz_id=?");

    // Cek apakah statement berhasil disiapkan
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query ubah: " . mysqli_error($conn));
    }

    // Bind parameter
    // 'ssssi' -> 4 string (program, kelas, semester1, semester2) dan 1 integer (id)
    mysqli_stmt_bind_param($stmt, "ssssi", $program, $kelas, $semester1, $semester2, $id);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        redirect("Ubah data Sukses!");
    } else {
        redirect("Ubah data Gagal! Error: " . mysqli_error($conn)); // Tampilkan error untuk debugging
    }

    // Tutup statement
    mysqli_stmt_close($stmt);
}

// ========== HAPUS DATA PROGRAM ==========
if (isset($_POST['phapus'], $_POST['tpid'])) {
    // Tidak perlu lagi fungsi s()
    $id = trim($_POST['tpid']);

    // Siapkan Prepared Statement
    $stmt = mysqli_prepare($conn, "DELETE FROM program_tahfidz WHERE program_tahfidz_id=?");

    // Cek apakah statement berhasil disiapkan
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query hapus: " . mysqli_error($conn));
    }

    // Bind parameter
    // 'i' -> 1 integer (id)
    mysqli_stmt_bind_param($stmt, "i", $id);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        redirect("Hapus data Sukses!");
    } else {
        redirect("Hapus data Gagal! Error: " . mysqli_error($conn)); // Tampilkan error untuk debugging
    }

    // Tutup statement
    mysqli_stmt_close($stmt);
}

// Tutup koneksi database di akhir skrip
mysqli_close($conn);
