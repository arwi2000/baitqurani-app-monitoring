<?php
// Sesuaikan path ini agar sesuai dengan lokasi file config.php Anda
// Jika get_santri_data.php berada di admin/tahfidz/
// dan config.php berada di config/, maka path relatifnya adalah '../../config/config.php'
include_once(__DIR__ . '/../../config/config.php');

header('Content-Type: application/json');

$response = [
    'success' => false,
    'nama_lengkap' => '',
    'kelas' => '',
    'jenis_kelamin' => '',
    'message' => ''
];

if (isset($_GET['nis']) && !empty($_GET['nis'])) {
    $nis = $_GET['nis'];

    // Query untuk mengambil data santri dari tabel 'users'
    $stmt = mysqli_prepare($conn, "SELECT nama_lengkap, kelas, jenis_kelamin FROM users WHERE nis = ? LIMIT 1");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $nis);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($data = mysqli_fetch_assoc($result)) {
            $response['success'] = true;
            $response['nama_lengkap'] = htmlspecialchars($data['nama_lengkap']);
            $response['kelas'] = htmlspecialchars($data['kelas']);
            // Mengubah nilai 'l'/'p' menjadi 'Laki-laki'/'Perempuan' jika diperlukan
            // Asumsi database menyimpan 'Laki-laki' atau 'Perempuan' secara langsung
            // Jika database menyimpan 'l' atau 'p', baris di bawah ini perlu diaktifkan
            // $response['jenis_kelamin'] = ($data['jenis_kelamin'] == 'l') ? 'Laki-laki' : (($data['jenis_kelamin'] == 'p') ? 'Perempuan' : htmlspecialchars($data['jenis_kelamin']));
            $response['jenis_kelamin'] = htmlspecialchars($data['jenis_kelamin']); // Jika database sudah menyimpan 'Laki-laki'/'Perempuan'
        } else {
            $response['message'] = 'NIS tidak ditemukan.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = 'Gagal menyiapkan query: ' . mysqli_error($conn);
    }
} else {
    $response['message'] = 'NIS tidak valid.';
}

mysqli_close($conn);
echo json_encode($response);
