<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Ambil path
    $stmt = $conn->prepare("SELECT file_path FROM program_files WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filePath);
    $stmt->fetch();
    $stmt->close();

    // Hapus file dari disk
    if ($filePath && file_exists($filePath)) {
        unlink($filePath);
    }

    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM program_files WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "File berhasil dihapus.";
    } else {
        echo "Gagal menghapus file.";
    }
}
