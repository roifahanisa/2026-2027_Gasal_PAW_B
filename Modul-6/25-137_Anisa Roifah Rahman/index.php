<?php
include "koneksi.php";
$result = mysqli_query($conn, "SELECT t.id, t.waktu_transaksi, t.keterangan, t.total,
                                      p.nama AS nama_pelanggan
                               FROM transaksi t
                               JOIN pelanggan p ON p.id = t.pelanggan_id
                               ORDER BY t.id");
?>

<!DOCTYPE html>
<html>
<head><title>Data Transaksi</title> <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">
    <h2>Master Transaksi</h2>
    <nav>
        <a class="btn primary" href="index.php">Daftar Transaksi</a>
        <a class="btn secondary" href="barang.php">Data Barang</a>
    </nav>

    <p>Pilih transaksi untuk melihat dan mengelola barang-barang pada detail transaksi.</p>

    <table>
        <tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th>Total</th><th>Aksi</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['waktu_transaksi']) ?></td>
            <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
            <td><?= htmlspecialchars($row['keterangan'] ?? '') ?></td>
            <td>Rp <?= number_format((int)$row['total'], 0, ',', '.') ?></td>
            <td><a class="btn primary" href="detail.php?id=<?= (int)$row['id'] ?>">Lihat Detail</a></td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>