<?php
include "koneksi.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT t.*, p.nama AS nama_pelanggan
                               FROM transaksi t
                               JOIN pelanggan p ON p.id = t.pelanggan_id
                               WHERE t.id = ?");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$transaksi = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$transaksi) {
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT td.barang_id, b.kode_barang, b.nama_barang,
                                      td.harga, td.qty,
                                      (td.harga * td.qty) AS subtotal
                               FROM transaksi_detail td
                               JOIN barang b ON b.id = td.barang_id
                               WHERE td.transaksi_id = ?
                               ORDER BY b.nama_barang");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$details = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Transaksi</title><link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

    <nav>
        <a class="btn secondary" href="index.php">Kembali</a>
        <a class="btn primary" href="barang.php">Data Barang</a>
    </nav>

    <h2>Detail Transaksi #<?= (int)$transaksi['id'] ?></h2>
    
    <p><b>Tanggal:</b> <?= htmlspecialchars($transaksi['waktu_transaksi']) ?></p>
    <p><b>Pelanggan:</b> <?= htmlspecialchars($transaksi['nama_pelanggan']) ?></p>
    <p><b>Keterangan:</b> <?= htmlspecialchars($transaksi['keterangan'] ?? '') ?></p>

    <h3>Daftar Barang dalam Transaksi</h3>

    <table>
        <tr>
            <th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Qty</th><th>Subtotal</th>
        </tr>

        <?php while ($d = mysqli_fetch_assoc($details)) { ?>
        <tr>
            <td><?= htmlspecialchars($d['kode_barang']) ?></td>
            <td><?= htmlspecialchars($d['nama_barang']) ?></td>
            <td>Rp <?= number_format((int)$d['harga'],0,',','.') ?></td>
            <td><?= (int)$d['qty'] ?></td>
            <td>Rp <?= number_format((int)$d['subtotal'],0,',','.') ?></td>
        </tr>
        <?php } ?>
    </table>

    <h3>
        Total Transaksi:
        Rp <?= number_format((int)$transaksi['total'], 0, ',', '.') ?>
    </h3>

    <a class="btn success"
       href="tambah_detail.php?transaksi_id=<?= (int)$id ?>">
        Tambah Barang ke Transaksi
    </a>

</div>
</body>
</html>