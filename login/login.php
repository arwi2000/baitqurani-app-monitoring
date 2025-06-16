<?php
session_start();
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bait Qurani - Login</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/login.css" />
</head>

<body class="d-flex justify-content-center align-items-center" style="height: 100vh;">
  <!-- Overlay background -->
  <div class="rectangle"></div>

  <!-- Login Form -->
  <form id="loginForm" action="proses-login.php" method="post" class="border rounded-4 d-grid text-center position-relative">
    <!-- Logos -->
    <img src="../assets/img/Logo2.png" alt="Logo Left" class="logo-left" />
    <img src="../assets/img/Logo1.png" alt="Logo Right" class="logo-right" />

    <!-- Title -->
    <h3 class="mb-5">LOGIN</h3>

    <!-- Pesan flash -->
    <?php if (!empty($message)): ?>
      <div class="alert alert-warning w-100">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <!-- Username Input -->
    <div class="mb-2 text-start w-100">
      <input type="text" name="nis" class="form-control" placeholder="Username / NIS" required />
    </div>

    <!-- Password Input -->
    <div class="mb-2 text-start w-100">
      <input type="password" name="password" class="form-control" placeholder="Password" required />
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-success">LOGIN</button>
  </form>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js"
    integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" 
    crossorigin="anonymous"></script>
  <script src="../assets/js/login.js"></script>
</body>

</html>
