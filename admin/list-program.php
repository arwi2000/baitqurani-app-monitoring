<?php
require_once '../config/config.php';

$result = $conn->query("SELECT * FROM program_files ORDER BY uploaded_at DESC");

echo '<ul>';
while ($row = $result->fetch_assoc()) {
    echo '<li>';
    echo '<a href="' . $row['file_path'] . '" target="_blank">' . htmlspecialchars($row['file_name']) . '</a>';
    echo ' - <a href="delete-program.php?id=' . $row['id'] . '" onclick="return confirm(\'Yakin ingin hapus?\')">Hapus</a>';
    echo '</li>';
}
echo '</ul>';
