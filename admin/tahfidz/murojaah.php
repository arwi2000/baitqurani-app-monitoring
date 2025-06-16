<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body>
    <div class="sidebar">
        <img src="../assets/img/sidebar-logo.png" alt="Logo" class="sidebar-logo" />
        <h2>Admin BQ</h2>
        <hr class="separator" />
        <div class="sidebar-text">
            <a href="../admin/dashboard.php"><img src="../assets/img/dashboard.svg" class="menu-icon" /> BERANDA</a>
            <a href="../admin/santri/index.php"><img src="../assets/img/student-card.svg" class="menu-icon" /> SANTRI</a>

            <div class="dropdown">
                <a href="#" class="dropdown-toggle">
                    <img src="../assets/img/quran-02.svg" class="menu-icon" /> TAHFIDZ <span class="arrow">&#9662;</span>
                </a>
                <div class="dropdown-menu">
                    <a href="../admin/tahfidz/ziyadah.php"><img src="../assets/img/quran-02.svg" class="menu-icon" />
                        ZIADAH</a>
                    <a href="/admin/murojaah-admin.html"><img src="../assets/img/quran-02.svg" class="menu-icon" />
                        MUROJA'AH</a>
                    <a href="/admin/tasmi-admin.html"><img src="../assets/img/quran-02.svg" class="menu-icon" />
                        TASMI'</a>
                </div>
            </div>

            <a href="/admin/rekap-admin.html"><img src="../assets/img/report.svg" class="menu-icon" /> REKAP</a>
        </div>
    </div>

    <div class="overlay"></div>

    <div class="main">
        <div class="header">
            <div class="header-texts">
                <a href="/login.html">
                    <img src="../assets/img/logout.svg" alt="Logout" class="logout-icon">
                </a>
                <div class="header-row">
                    <img src="../assets/img/hamburger.svg" class="hamburger-icon" />
                    <h1>BQ TAHFIDZ</h1>
                </div>
            </div>
            <h3 class="header-subtitle">BERANDA</h3>
            <div class="header-main"> </div>
        </div>
    </div>

        <script src="../assets/js/script.js"></script>
</body>

</html>