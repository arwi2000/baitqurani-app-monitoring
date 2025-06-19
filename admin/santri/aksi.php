<?php
session_start(); // Memulai session
define('BASE_DIR', realpath(__DIR__ . '/../../')); // Mendefinisikan BASE_DIR
include_once(BASE_DIR . '/config/config.php'); // Memasukkan file konfigurasi database

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<script>alert('Akses ditolak!'); location.href='" . BASE_URL . "/login/login.php';</script>");
}
// ======================================
// DEFINISI FUNGSI-FUNGSI UTAMA
// ======================================

/**
 * Mengunggah file foto ke direktori yang ditentukan.
 * @param string $input_name Nama input file dari form (e.g., 'tfoto').
 * @param string $upload_dir Direktori tempat file akan disimpan.
 * @param string &$generated_filename Variabel referensi untuk menyimpan nama file yang di-generate.
 * @return bool True jika upload berhasil, false jika tidak.
 */
function uploadFoto($input_name, $upload_dir, &$generated_filename)
{
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
    $generated_filename = uniqid() . '.' . $ext;
    $target_path = "$upload_dir/$generated_filename";

    return move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_path);
}

/**
 * Mengalihkan halaman setelah menampilkan pesan alert.
 * @param string $msg Pesan yang akan ditampilkan di alert.
 * @param string $location Lokasi redirect (default ke index.php relatif terhadap folder saat ini)
 */
function redirect($msg, $location = 'index.php')
{
    exit("<script>alert('$msg'); location.href='$location';</script>");
}

/**
 * Menghapus file foto lama dari direktori upload berdasarkan NIS santri.
 * @param string $nis NIS santri untuk mencari nama file foto di database.
 * @param mysqli $conn Objek koneksi database.
 */
function hapusFoto($nis, $conn)
{
    $stmt = mysqli_prepare($conn, "SELECT foto FROM users WHERE nis=?");
    if ($stmt === false) {
        error_log("Error preparing statement in hapusFoto: " . mysqli_error($conn));
        return;
    }
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $foto_lama = $row['foto'];
        if (!empty($foto_lama)) {
            $path = BASE_DIR . "/uploads/" . $foto_lama;
            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }
        }
    }
    mysqli_stmt_close($stmt);
}

// ======================================
// VERIFIKASI PERAN ADMIN
// ======================================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    redirect('Akses ditolak.', BASE_URL . '/login/login.php');
}


// ======================================
// LOGIKA UTAMA APLIKASI
// Menangani operasi Tambah, Ubah, Hapus berdasarkan POST request
// ======================================

// ========== TAMBAH DATA SANTRI ==========
if (isset($_POST['bsimpan'])) {
    $nis = trim($_POST['tnis']);
    $nama = trim($_POST['tnama']);
    $kelas = trim($_POST['tkelas']);
    $jk = trim($_POST['tjeniskelamin']);
    $role = trim($_POST['trole']);
    $pass = password_hash($_POST['tpassword'], PASSWORD_DEFAULT);

    // Validate unique NIS
    $stmt_check_nis = mysqli_prepare($conn, "SELECT nis FROM users WHERE nis = ?");
    if ($stmt_check_nis === false) {
        redirect("Gagal mempersiapkan query cek NIS: " . mysqli_error($conn), 'index.php');
    }
    mysqli_stmt_bind_param($stmt_check_nis, "s", $nis);
    mysqli_stmt_execute($stmt_check_nis);
    mysqli_stmt_store_result($stmt_check_nis);

    if (mysqli_stmt_num_rows($stmt_check_nis) > 0) {
        mysqli_stmt_close($stmt_check_nis);
        redirect("Simpan data gagal! NIS tidak boleh sama atau sudah ada.", 'index.php');
    }
    mysqli_stmt_close($stmt_check_nis);

    $foto_nama_file = '';
    if (uploadFoto('tfoto', BASE_DIR . '/uploads', $foto_nama_file)) {
        // Foto uploaded
    } else {
        $foto_nama_file = '';
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO users (nis, nama_lengkap, kelas, jenis_kelamin, role, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query insert: " . mysqli_error($conn), 'index.php');
    }
    mysqli_stmt_bind_param($stmt, "sssssss", $nis, $nama, $kelas, $jk, $role, $pass, $foto_nama_file);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Simpan data Sukses!", 'index.php');
    } else {
        redirect("Simpan data Gagal! Error: " . mysqli_stmt_error($stmt), 'index.php');
    }
    mysqli_stmt_close($stmt);
}

// ========== UBAH DATA SANTRI ==========
if (isset($_POST['bubah'])) {
    if (!isset($_POST['tnis']) || empty(trim($_POST['tnis']))) {
        redirect("NIS tidak ditemukan untuk proses ubah data. Pastikan NIS dikirimkan dari form.", 'index.php');
    }
    $nis_identifikasi = trim($_POST['tnis']);

    $nama = trim($_POST['tnama']);
    $kelas = trim($_POST['tkelas']);
    $jk = trim($_POST['tjeniskelamin']);
    $role = trim($_POST['trole']);

    $fields_to_update = [];
    $types = "";
    $params = [];

    $fields_to_update[] = "nama_lengkap=?";
    $types .= "s";
    $params[] = $nama;
    $fields_to_update[] = "kelas=?";
    $types .= "s";
    $params[] = $kelas;
    $fields_to_update[] = "jenis_kelamin=?";
    $types .= "s";
    $params[] = $jk;
    $fields_to_update[] = "role=?";
    $types .= "s";
    $params[] = $role;

    if (!empty($_POST['tpassword'])) {
        $fields_to_update[] = "password=?";
        $types .= "s";
        $hashed_password = password_hash($_POST['tpassword'], PASSWORD_DEFAULT);
        $params[] = $hashed_password;
    }

    $foto_baru_nama_file = '';
    if (isset($_FILES['tfoto']) && $_FILES['tfoto']['error'] !== UPLOAD_ERR_NO_FILE) {
        if (uploadFoto('tfoto', BASE_DIR . '/uploads', $foto_baru_nama_file)) {
            hapusFoto($nis_identifikasi, $conn);
            $fields_to_update[] = "foto=?";
            $types .= "s";
            $params[] = $foto_baru_nama_file;
        } else {
            redirect("Ubah data Gagal! Error upload foto.", 'index.php');
        }
    }

    if (empty($fields_to_update)) {
        redirect("Tidak ada data yang diubah atau dipilih untuk diupdate.", 'index.php');
    }

    $sql_set_clause = implode(", ", $fields_to_update);
    $sql = "UPDATE users SET $sql_set_clause WHERE nis=?";

    $types .= "s";
    $params[] = $nis_identifikasi;

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query ubah: " . mysqli_error($conn), 'index.php');
    }

    $bind_params = [$stmt, $types];
    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    call_user_func_array('mysqli_stmt_bind_param', $bind_params);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            redirect("Ubah data Sukses!", 'index.php');
        } else {
            redirect("Ubah data berhasil, tetapi tidak ada perubahan karena data yang diinput sama dengan data yang sudah ada.", 'index.php');
        }
    } else {
        redirect("Ubah data Gagal! Error: " . mysqli_stmt_error($stmt), 'index.php');
    }
    mysqli_stmt_close($stmt);
}

// ========== HAPUS DATA SANTRI ==========
if (isset($_POST['bhapus'], $_POST['nis'])) {
    $nis_to_delete = trim($_POST['nis']);

    hapusFoto($nis_to_delete, $conn);

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE nis=?");
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query hapus: " . mysqli_error($conn), 'index.php');
    }
    mysqli_stmt_bind_param($stmt, "s", $nis_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            redirect("Hapus data Sukses!", 'index.php');
        } else {
            redirect("Hapus data gagal! Data tidak ditemukan.", 'index.php');
        }
    } else {
        redirect("Hapus data Gagal! Error: " . mysqli_stmt_error($stmt), 'index.php');
    }
    mysqli_stmt_close($stmt);
}

// Close database connection
mysqli_close($conn);
