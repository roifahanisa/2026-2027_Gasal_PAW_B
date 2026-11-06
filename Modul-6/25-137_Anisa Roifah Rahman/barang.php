<?php
include "koneksi.php";

$pesan = "";

if (isset($_GET['status']) && $_GET['status'] === 'hapus') {
    $pesan = "Data barang berhasil dihapus.";
}

if (isset($_GET['error']) && $_GET['error'] === 'dipakai') {
    $pesan = "Barang tidak dapat dihapus karena masih digunakan pada transaksi.";
}

$result = mysqli_query($conn, "SELECT b.*, s.nama AS nama_supplier
                               FROM barang b
                               JOIN supplier s ON s.id = b.supplier_id
                               ORDER BY b.id");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Barang</title><link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

    <nav>
        <a class="btn primary" href="index.php">Master Transaksi</a>
    </nav>

    <h2>Data Barang</h2>

    <?php if ($pesan) { ?>
        <div class="notice">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php } ?>

<table>
    <tr>
        <th>ID</th><th>Kode</th><th>Nama Barang</th><th>Harga</th><th>Stok</th><th>Supplier</th><th>Aksi</th>
    </tr>

    <?php while ($b = mysqli_fetch_assoc($result)) { ?>
    <tr>
    <td>
        <?= (int)$b['id'] ?>
    </td>
    <td>
        <?= htmlspecialchars($b['kode_barang']) ?>
    </td>
    <td>
        <?= htmlspecialchars($b['nama_barang']) ?>
    </td>
    <td>
        Rp <?= number_format((int)$b['harga'], 0, ',', '.') ?>
    </td>
    <td>
        <?= (int)$b['stok'] ?>
    </td>
    <td>
        <?= htmlspecialchars($b['nama_supplier']) ?>
    </td>

<td>
    <form method="POST"
    action="hapus_barang.php"
    onsubmit="return confirm('Yakin ingin menghapus barang ini?');">

    <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">

    <button class="btn danger" type="submit">Hapus</button>

    </form>
</td>
</tr>
        <?php } ?>
    </table>
</div>
</body>
</html>