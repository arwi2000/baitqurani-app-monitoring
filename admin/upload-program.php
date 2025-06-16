<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmp  = $_FILES['file']['tmp_name'];
    $fileType = $_FILES['file']['type'];
    $targetDir = '../uploads/';
    $filePath = $targetDir . basename($fileName);

    // Validasi nama file tidak duplikat
    if (file_exists($filePath)) {
        echo "File sudah ada.";
        exit;
    }

    if (move_uploaded_file($fileTmp, $filePath)) {
        $stmt = $conn->prepare("INSERT INTO program_files (file_name, file_path, file_type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fileName, $filePath, $fileType);

        if ($stmt->execute()) {
            echo "Upload berhasil.";
        } else {
            echo "Gagal simpan ke database.";
        }
    } else {
        echo "Gagal upload file.";
    }
}
