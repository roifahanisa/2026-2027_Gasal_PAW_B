<?php
include 'koneksi.php';

$tanggal_mulai = isset($_GET['tanggal_mulai'])
    ? $_GET['tanggal_mulai'] : date('Y-m-01');

$tanggal_selesai = isset($_GET['tanggal_selesai'])
    ? $_GET['tanggal_selesai'] : date('Y-m-d');

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

$tanggal = [];
$pendapatan = [];
$total_pendapatan = 0;

while ($data = mysqli_fetch_assoc($result)) {
    $tanggal[] = $data['tanggal'];
    $pendapatan[] = $data['pendapatan'];
    $total_pendapatan += $data['pendapatan'];
}

$query_pelanggan = "SELECT COUNT(DISTINCT pelanggan_id) AS jumlah
                    FROM transaksi
                    WHERE DATE(waktu_transaksi) BETWEEN ? AND ?";

$stmt2 = mysqli_prepare($koneksi, $query_pelanggan);
mysqli_stmt_bind_param($stmt2, "ss",
    $tanggal_mulai, $tanggal_selesai);
mysqli_stmt_execute($stmt2);

$data_pelanggan = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt2)
);

$jumlah_pelanggan = $data_pelanggan['jumlah'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reporting Penjualan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="container">

    <h2 class="text-center mb-4">Laporan Penjualan</h2>

    <div class="card no-print">
        <div class="card-body">

        <form method="GET">
        <div class="row">

        <div class="col-md-4">
            <label>Tanggal Mulai</label>
            <input type="date"
                    name="tanggal_mulai"
                    class="form-control"
                    value="<?= htmlspecialchars($tanggal_mulai) ?>"
                	required>
            </div>

        <div class="col-md-4">
            <label>Tanggal Selesai</label>
            <input type="date"
                    name="tanggal_selesai"
                    class="form-control"
                    value="<?= htmlspecialchars($tanggal_selesai) ?>"
                    required>
        </div>

        <div class="col-md-4 mt-4">
            <button type="submit"
                    class="btn btn-primary">
                        Tampilkan
            </button>

            <button type="button"
                    onclick="window.print()"
                    class="btn btn-success">
                Cetak
            </button>

        <a href="export_excel.php?tanggal_mulai=<?= urlencode($tanggal_mulai) ?>&tanggal_selesai=<?= urlencode($tanggal_selesai) ?>"
            class="btn btn-warning">
             Excel</a>
        </div>
</div>
</form>
</div>
</div>

<div class="card">
    <div class="card-body">
		<h5>Grafik Pendapatan Harian</h5>
        <canvas id="grafikPenjualan"></canvas>

    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5>Rekapitulasi Penjualan Harian</h5>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Tanggal Transaksi</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>

        <tbody>
            <?php
            $no = 1;

            for ($i = 0; $i < count($tanggal); $i++) {
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($tanggal[$i]) ?></td>
                    <td>Rp <?= number_format($pendapatan[$i], 0, ',', '.') ?></td>
                </tr>

            <?php } ?>
				<?php if (count($tanggal) == 0) { ?>
                    <tr><td colspan="3" class="text-center">Tidak ada data penjualan.</td></tr>
                <?php } ?>

        </tbody>
        </table>
</div></div>

<div class="row">
    <div class="col-md-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Jumlah Pelanggan Unik</h5>
                <h3><?= $jumlah_pelanggan ?> Pelanggan</h3>
             </div>
        </div>
    </div>

<div class="col-md-6">
    <div class="card bg-success text-white">
        <div class="card-body">
            <h5>Total Pendapatan Keseluruhan</h5>
            <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
        </div>
    </div>
</div>
</div>
</div>

<script>
const ctx = document.getElementById('grafikPenjualan');

new Chart(ctx, {
    type: 'bar',

    data: {
        labels: <?= json_encode($tanggal) ?>,

        datasets: [{
            label: 'Total Pendapatan',
            data: <?= json_encode($pendapatan) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1
        }]
    },

    options: {
        responsive: true,

        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Total Pendapatan (Rp)'
                }
            },

            x: {
                title: {
                    display: true,
                    text: 'Tanggal Transaksi'
                }
            }
        }
    }
});
</script>
</body>
</html>