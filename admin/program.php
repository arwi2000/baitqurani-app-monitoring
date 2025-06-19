<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../'));
include_once(BASE_DIR . '/config/config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<script>alert('Akses ditolak.'); location.href='" . BASE_URL . "/login/login.php';</script>");
}

function redirect($msg, $location = 'dashboard.php')
{
    exit("<script>alert('$msg'); location.href='$location';</script>");
}

if (isset($_POST['psimpan'])) {
    $program = trim($_POST['tpprogram']);
    $kelas = trim($_POST['tpkelas']);
    $semester1 = trim($_POST['tpsemester1']);
    $semester2 = trim($_POST['tpsemester2']);

    $stmt = mysqli_prepare($conn, "INSERT INTO program_tahfidz (program, kelas, semester_1, semester_2) VALUES (?, ?, ?, ?)");

    if ($stmt === false) {
        redirect("Gagal mempersiapkan query tambah: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssss", $program, $kelas, $semester1, $semester2);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Simpan data Sukses!");
    } else {
        redirect("Simpan data Gagal! Error: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
}

if (isset($_POST['pubah'])) {
    $id = trim($_POST['tpid']);
    $program = trim($_POST['tpprogram']);
    $kelas = trim($_POST['tpkelas']);
    $semester1 = trim($_POST['tpsemester1']);
    $semester2 = trim($_POST['tpsemester2']);

    


    $stmt = mysqli_prepare($conn, "UPDATE program_tahfidz SET program=?, kelas=?, semester_1=?, semester_2=? WHERE program_tahfidz_id=?");

    if ($stmt === false) {
        redirect("Gagal mempersiapkan query ubah: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssi", $program, $kelas, $semester1, $semester2, $id);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Ubah data Sukses!");
    } else {
        redirect("Ubah data Gagal! Error: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
}

if (isset($_POST['phapus'], $_POST['tpid'])) {
    $id = trim($_POST['tpid']);

    $stmt = mysqli_prepare($conn, "DELETE FROM program_tahfidz WHERE program_tahfidz_id=?");

    if ($stmt === false) {
        redirect("Gagal mempersiapkan query hapus: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Hapus data Sukses!");
    } else {
        redirect("Hapus data Gagal! Error: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
