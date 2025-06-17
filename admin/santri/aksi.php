<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../../'));
include_once(BASE_DIR . '/config/config.php');

if ($_SESSION['role'] !== 'admin') {
    exit("<script>alert('Akses ditolak.'); location.href='" . BASE_URL . "/login/login.php';</script>");
}

function s($c, $d)
{
    return mysqli_real_escape_string($c, trim($d));
}

function uploadFoto($input, $dir, &$nama)
{
    if (!isset($_FILES[$input]) || $_FILES[$input]['error'] !== UPLOAD_ERR_OK) return false;
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $ext = pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION);
    $nama = uniqid() . ".$ext";
    return move_uploaded_file($_FILES[$input]['tmp_name'], "$dir/$nama");
}

function redirect($msg)
{
    exit("<script>alert('$msg'); location.href='index.php';</script>");
}

function hapusFoto($nis, $conn)
{
    $r = mysqli_query($conn, "SELECT foto FROM users WHERE nis='$nis'");
    if ($r && $f = mysqli_fetch_assoc($r)['foto']) {
        $path = BASE_DIR . "/uploads/$f";
        if (file_exists($path)) unlink($path);
    }
}

// ========== TAMBAH ==========
if (isset($_POST['bsimpan'])) {
    [$nis, $nama, $kelas, $jk, $role] = array_map(fn($f) => s($conn, $_POST[$f]), ['tnis', 'tnama', 'tkelas', 'tjeniskelamin', 'trole']);
    $pass = password_hash($_POST['tpassword'], PASSWORD_DEFAULT);
    $foto = '';
    uploadFoto('tfoto', BASE_DIR . '/uploads', $foto);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (nis, nama_lengkap, kelas, jenis_kelamin, role, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssssss", $nis, $nama, $kelas, $jk, $role, $pass, $foto);
    redirect(mysqli_stmt_execute($stmt) ? "Simpan data Sukses!" : "Simpan data Gagal!");
}

// ========== UBAH ==========
if (isset($_POST['bubah'])) {
    [$nis, $nama, $kelas, $jk, $role] = array_map(fn($f) => s($conn, $_POST[$f]), ['tnis', 'tnama', 'tkelas', 'tjeniskelamin', 'trole']);
    $fields = "nama_lengkap=?, kelas=?, jenis_kelamin=?, role=?";
    $types = "ssss";
    $params = [$nama, $kelas, $jk, $role];

    if (!empty($_POST['tpassword'])) {
        $fields .= ", password=?";
        $types .= "s";
        $params[] = password_hash($_POST['tpassword'], PASSWORD_DEFAULT);
    }

    $foto = '';
    if (uploadFoto('tfoto', BASE_DIR . '/uploads', $foto)) {
        hapusFoto($nis, $conn);
        $fields .= ", foto=?";
        $types .= "s";
        $params[] = $foto;
    }

    $fields .= " WHERE nis=?";
    $types .= "s";
    $params[] = $nis;

    $stmt = mysqli_prepare($conn, "UPDATE users SET $fields");
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    redirect(mysqli_stmt_execute($stmt) ? "Ubah data Sukses!" : "Ubah data Gagal!");
}

// ========== HAPUS ==========
if (isset($_POST['bhapus'], $_POST['nis'])) {
    $nis = s($conn, $_POST['nis']);
    hapusFoto($nis, $conn);
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE nis=?");
    mysqli_stmt_bind_param($stmt, "s", $nis);
    redirect(mysqli_stmt_execute($stmt) ? "Hapus data Sukses!" : "Hapus data Gagal!");
}
