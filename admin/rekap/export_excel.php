<?php
session_start();
define('BASE_DIR', realpath(__DIR__ . '/../../'));
include_once(BASE_DIR . '/config/config.php');

// Cek apakah user sudah login dan memiliki role 'admin'
if (!isset($_SESSION['nis']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Akses ditolak. Silakan login sebagai admin.";
    $_SESSION['message_type'] = "danger";
    header("Location: " . BASE_URL . "/login/login.php");
    exit();
}

// --- PENTING: Untuk debugging, aktifkan ini sementara ---
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// -------------------------------------------------------

// Set header untuk file CSV (Comma Separated Values) dengan encoding UTF-8
// Perhatikan Content-Type dan ekstensi filename tetap .csv
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="rekap_data_tahfidz_' . date('Ymd_His') . '.csv"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tambahkan BOM (Byte Order Mark) untuk UTF-8
// Ini sangat penting agar Excel (terutama versi Windows) mengenali encoding UTF-8
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Pastikan koneksi database juga disetel ke UTF-8mb4 untuk kompatibilitas karakter penuh
if (isset($conn)) {
    mysqli_set_charset($conn, "utf8mb4"); // Atau "utf8" jika database Anda tidak mendukung utf8mb4
}

// Definisi header kolom
$headers = array('No', 'NIS', 'Nama Lengkap', 'Kelas', 'Jenis Kelamin', 'Tanggal Tahfidz', 'Jenis Tahfidz', 'Halaman', 'Juz');

// Tulis header CSV
// Delimiter default fputcsv adalah koma (,)
fputcsv($output, $headers);

// Bangun query SQL dengan filter
$sql = "SELECT
            u.nis,
            u.nama_lengkap,
            u.kelas,
            u.jenis_kelamin,
            t.tanggal,
            t.jenis_tahfidz,
            t.halaman,
            t.juz
        FROM
            users AS u
        LEFT JOIN
            tahfidz AS t ON u.nis = t.nis
        WHERE 1=1";

$params = [];
$types = "";

// Tambahkan kondisi filter jika ada parameter di URL
if (isset($_GET['kelas']) && $_GET['kelas'] != '') {
    $sql .= " AND u.kelas = ?";
    $params[] = $_GET['kelas'];
    $types .= "s";
}
if (isset($_GET['jenis_kelamin']) && $_GET['jenis_kelamin'] != '') {
    $sql .= " AND u.jenis_kelamin = ?";
    $params[] = $_GET['jenis_kelamin'];
    $types .= "s";
}
if (isset($_GET['jenis_tahfidz']) && $_GET['jenis_tahfidz'] != '') {
    $sql .= " AND t.jenis_tahfidz = ?";
    $params[] = $_GET['jenis_tahfidz'];
    $types .= "s";
}
if (isset($_GET['tanggal']) && $_GET['tanggal'] != '') {
    $sql .= " AND t.tanggal = ?";
    $params[] = $_GET['tanggal'];
    $types .= "s";
}

$sql .= " ORDER BY u.nis ASC, t.tanggal DESC";

// Persiapkan dan eksekusi statement
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    error_log("Error preparing statement for export: " . mysqli_error($conn));
    fputcsv($output, ['Error: Gagal mempersiapkan query data.']);
    fclose($output);
    exit();
}

if (!empty($params)) {
    // Menggunakan call_user_func_array untuk mysqli_stmt_bind_param
    // Ini lebih kompatibel dengan berbagai versi PHP
    call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt, $types], $params));
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$no = 1;
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Pembersihan dan pemformatan data untuk Excel
        // Ganti 'Belum Ada' dengan string kosong agar sel di Excel kosong
        // Pastikan tanggal diformat YYYY-MM-DD agar dikenali Excel
        $tanggal_tahfidz = $row['tanggal'] ? date('Y-m-d', strtotime($row['tanggal'])) : '';
        $jenis_tahfidz = $row['jenis_tahfidz'] ?? '';
        $halaman = $row['halaman'] ?? '';
        $juz = $row['juz'] ?? '';

        $data_row = [
            $no++,
            $row['nis'],
            $row['nama_lengkap'],
            $row['kelas'],
            $row['jenis_kelamin'],
            $tanggal_tahfidz,
            $jenis_tahfidz,
            $halaman,
            $juz
        ];

        // Tulis baris data ke CSV
        // fputcsv akan otomatis menangani quoting jika ada koma di dalam data
        fputcsv($output, $data_row);
    }
} else {
    // Jika tidak ada data, tetap tulis header dan satu baris pesan "Tidak ada data"
    fputcsv($output, ['Tidak ada data ditemukan untuk kriteria filter yang dipilih.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
fclose($output);
exit();
