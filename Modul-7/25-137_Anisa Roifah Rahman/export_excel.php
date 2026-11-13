<?php
include 'koneksi.php';

$tanggal_mulai = $_GET['tanggal_mulai'];
$tanggal_selesai = $_GET['tanggal_selesai'];

$query = "SELECT DATE(waktu_transaksi) AS tanggal,
          SUM(total) AS pendapatan
          FROM transaksi
          WHERE DATE(waktu_transaksi) BETWEEN ? AND ?
          GROUP BY DATE(waktu_transaksi)
          ORDER BY tanggal ASC";

$stmt = mysqli_prepare($koneksi, $query);

mysqli_stmt_bind_param($stmt, "ss",
    $tanggal_mulai, $tanggal_selesai);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_penjualan.xls");

echo "<table border='1'>";
echo "<tr>";
echo "<th>No</th>";
echo "<th>Tanggal Transaksi</th>";
echo "<th>Total Pendapatan</th>";
echo "</tr>";

$no = 1;

while ($data = mysqli_fetch_assoc($result)) {

    echo "<tr>";

    echo "<td>" . $no++ . "</td>";
    echo "<td>" . htmlspecialchars($data['tanggal']) . "</td>";
    echo "<td>" . $data['pendapatan'] . "</td>";

    echo "</tr>";
}

echo "</table>";
?>