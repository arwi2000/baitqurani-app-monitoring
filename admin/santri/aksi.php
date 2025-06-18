<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../../'));
include_once(BASE_DIR . '/config/config.php'); // Menggunakan config.php Anda

// Verifikasi peran admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit("<script>alert('Akses ditolak.'); location.href='" . BASE_URL . "/login/login.php';</script>");
}

/**
 * Mengunggah file foto ke direktori yang ditentukan.
 * @param string $input_name Nama input file dari form (e.g., 'tfoto').
 * @param string $upload_dir Direktori tempat file akan disimpan.
 * @param string &$generated_filename Variabel referensi untuk menyimpan nama file yang di-generate.
 * @return bool True jika upload berhasil, false jika tidak.
 */
function uploadFoto($input_name, $upload_dir, &$generated_filename)
{
    // Cek apakah file diupload dan tidak ada error
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Buat direktori upload jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Dapatkan ekstensi file asli
    $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
    // Buat nama file unik
    $generated_filename = uniqid() . '.' . $ext;
    $target_path = "$upload_dir/$generated_filename";

    // Pindahkan file yang diupload ke direktori tujuan
    return move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_path);
}

/**
 * Mengalihkan halaman setelah menampilkan pesan alert.
 * @param string $msg Pesan yang akan ditampilkan di alert.
 * @param string $location Lokasi redirect (default ke index.php)
 */
function redirect($msg, $location = 'index.php') // Menambah parameter $location
{
    exit("<script>alert('$msg'); location.href='$location';</script>");
}

/**
 * Menghapus file foto lama dari direktori upload.
 * @param string $nis NIS santri untuk mencari nama file foto di database.
 * @param mysqli $conn Objek koneksi database.
 */
function hapusFoto($nis, $conn)
{
    // Ambil nama file foto dari database menggunakan Prepared Statements
    $stmt = mysqli_prepare($conn, "SELECT foto FROM users WHERE nis=?");
    if ($stmt === false) {
        // Handle error jika prepare gagal
        error_log("Error preparing statement in hapusFoto: " . mysqli_error($conn));
        return; // Keluar dari fungsi
    }
    mysqli_stmt_bind_param($stmt, "s", $nis);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $foto_lama = $row['foto'];
        if (!empty($foto_lama)) {
            $path = BASE_DIR . "/uploads/" . $foto_lama;
            // Hapus file jika ada dan merupakan file yang valid (bukan direktori)
            if (file_exists($path) && is_file($path)) {
                unlink($path);
            }
        }
    }
    mysqli_stmt_close($stmt);
}


// ========== TAMBAH DATA SANTRI ==========
if (isset($_POST['bsimpan'])) {
    // Trim() untuk membersihkan spasi ekstra. Tidak perlu s() karena Prepared Statements sudah aman.
    $nis = trim($_POST['tnis']);
    $nama = trim($_POST['tnama']);
    $kelas = trim($_POST['tkelas']);
    $jk = trim($_POST['tjeniskelamin']);
    $role = trim($_POST['trole']);
    $pass = password_hash($_POST['tpassword'], PASSWORD_DEFAULT);

    $foto_nama_file = ''; // Inisialisasi nama file foto
    // Cek apakah ada file foto yang diupload
    if (uploadFoto('tfoto', BASE_DIR . '/uploads', $foto_nama_file)) {
        // Foto berhasil diupload, $foto_nama_file berisi nama file baru
    } else {
        // Tidak ada foto diupload atau ada error upload, set ke default atau biarkan kosong
        $foto_nama_file = '';
    }

    // Menggunakan Prepared Statements untuk INSERT
    $stmt = mysqli_prepare($conn, "INSERT INTO users (nis, nama_lengkap, kelas, jenis_kelamin, role, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "sssssss", $nis, $nama, $kelas, $jk, $role, $pass, $foto_nama_file);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Simpan data Sukses!");
    } else {
        redirect("Simpan data Gagal! Error: " . mysqli_error($conn)); // Tampilkan error untuk debugging
    }
    mysqli_stmt_close($stmt);
}

// ========== UBAH DATA SANTRI ==========
if (isset($_POST['bubah'])) {
    // Trim() untuk membersihkan spasi ekstra. Tidak perlu s().
    $nis_lama = trim($_POST['tnis']); // NIS lama dari hidden input
    $nama = trim($_POST['tnama']);
    $kelas = trim($_POST['tkelas']);
    $jk = trim($_POST['tjeniskelamin']);
    $role = trim($_POST['trole']);

    $fields = "nama_lengkap=?, kelas=?, jenis_kelamin=?, role=?";
    $types = "ssss";
    $params = [&$nama, &$kelas, &$jk, &$role]; // Penting: Bind by reference untuk params di UPDATE/SELECT dynamic

    // Handle password update jika diisi
    if (!empty($_POST['tpassword'])) {
        $fields .= ", password=?";
        $types .= "s";
        $hashed_password = password_hash($_POST['tpassword'], PASSWORD_DEFAULT);
        $params[] = &$hashed_password; // Bind by reference
    }

    $foto_baru_nama_file = '';
    // Cek apakah ada foto baru yang diupload
    if (uploadFoto('tfoto', BASE_DIR . '/uploads', $foto_baru_nama_file)) {
        // Jika ada foto baru diupload, hapus foto lama terlebih dahulu
        hapusFoto($nis_lama, $conn);
        $fields .= ", foto=?";
        $types .= "s";
        $params[] = &$foto_baru_nama_file; // Bind by reference
    }
    // Jika tidak ada foto baru diupload, kolom 'foto' tidak akan dimasukkan ke dalam UPDATE query

    $fields .= " WHERE nis=?";
    $types .= "s";
    $params[] = &$nis_lama; // Bind by reference

    // Menggunakan Prepared Statements untuk UPDATE
    $sql = "UPDATE users SET $fields"; // Query dibangun secara dinamis
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query ubah: " . mysqli_error($conn));
    }

    // Memanggil mysqli_stmt_bind_param dengan call_user_func_array untuk parameter dinamis
    // Array params harus diawali dengan $stmt dan string types
    array_unshift($params, $stmt, $types);
    call_user_func_array('mysqli_stmt_bind_param', $params);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Ubah data Sukses!");
    } else {
        redirect("Ubah data Gagal! Error: " . mysqli_error($conn)); // Tampilkan error untuk debugging
    }
    mysqli_stmt_close($stmt);
}

// ========== HAPUS DATA SANTRI ==========
if (isset($_POST['bhapus'], $_POST['nis'])) {
    // Trim() untuk membersihkan spasi ekstra. Tidak perlu s().
    $nis_to_delete = trim($_POST['nis']);

    // Hapus file foto terkait sebelum menghapus record dari database
    hapusFoto($nis_to_delete, $conn);

    // Menggunakan Prepared Statements untuk DELETE
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE nis=?");
    if ($stmt === false) {
        redirect("Gagal mempersiapkan query hapus: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $nis_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        redirect("Hapus data Sukses!");
    } else {
        redirect("Hapus data Gagal! Error: " . mysqli_error($conn)); // Tampilkan error untuk debugging
    }
    mysqli_stmt_close($stmt);
}

// Tutup koneksi database di akhir skrip
mysqli_close($conn);
