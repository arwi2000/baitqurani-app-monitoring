<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan Santri</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      margin: 0;
      background-color: #f3f4f6;
    }

    .header {
      background: url('https://via.placeholder.com/1500x250/2e7d32/ffffff?text=Header+Background') no-repeat center;
      background-size: cover;
      padding: 20px;
      display: flex;
      align-items: center;
      color: white;
    }

    .header img {
      width: 80px;
      height: 80px;
      margin-right: 20px;
    }

    .container {
      display: flex;
      padding: 20px;
      gap: 20px;
      flex-wrap: wrap;
    }

    .profile-card {
      background: white;
      border-radius: 16px;
      padding: 20px;
      width: 250px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .profile-card img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }

    .profile-card h2 {
      font-size: 18px;
      margin: 10px 0 4px;
    }

    .profile-card .verified {
      color: green;
      font-size: 14px;
    }

    .profile-card p {
      margin: 2px 0;
      font-size: 14px;
    }

    .print-btn {
      margin-top: 15px;
      background-color: #198754;
      color: white;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
    }

    .report-table {
      background: white;
      flex: 1;
      border-radius: 16px;
      padding: 20px;
      overflow-x: auto;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    table thead tr {
      background-color: #f5f5f5;
    }

    table th,
    table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }

    table th {
      font-weight: 600;
    }

    @media(max-width: 768px) {
      .container {
        flex-direction: column;
        align-items: center;
      }
    }
  </style>
</head>
<body>

  <div class="header">
    <img src="https://via.placeholder.com/80x80.png?text=Logo" alt="Logo">
    <div>
      <h1 style="margin: 0; font-size: 24px;">Pondok Pesantren Bait Qur'ani</h1>
      <p style="margin: 0; font-size: 14px;">Ma’had Ilmi untuk Studi Islam & Tahfidz</p>
    </div>
  </div>

  <div class="container">
    <!-- Profile Card -->
    <div class="profile-card">
      <img src="https://via.placeholder.com/100.png?text=Foto" alt="Foto Santri">
      <h2>Nadha Khaliza</h2>
      <div class="verified">✔ 221011402472</div>
      <p>IX (Sembilan)</p>
      <p>Perempuan</p>
      <button class="print-btn" onclick="window.print()">Print</button>
    </div>

    <!-- Report Table -->
    <div class="report-table">
      <table>
        <thead>
          <tr>
            <th rowspan="2">Tanggal</th>
            <th colspan="2">Ziyadah</th>
            <th colspan="2">Muroja'ah</th>
            <th colspan="2">Tasmī'</th>
          </tr>
          <tr>
            <th>Juz</th>
            <th>Hal</th>
            <th>Juz</th>
            <th>Hal</th>
            <th>Juz</th>
            <th>Hal</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>01-03-2003</td>
            <td></td><td></td><td></td><td></td><td></td><td></td>
          </tr>
          <!-- Tambah baris lain di sini -->
          <tr><td colspan="7" style="height: 300px;"></td></tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
