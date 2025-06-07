<?php
session_start();
if (!isset($_SESSION['nis'])) {
    header("Location: dashboard-admin.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard - Bait Qurani</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container mt-5">
    <h2>Selamat datang, <?= htmlspecialchars($_SESSION['nis']); ?></h2>
    <p>Role: <?= htmlspecialchars($_SESSION['role']); ?></p>
    <a href="logout.php" class="btn btn-danger">Logout</a>
  </div>
</body>
</html>
