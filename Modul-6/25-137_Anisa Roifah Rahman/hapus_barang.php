<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['id'])) {
    header("Location: barang.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: barang.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS jumlah
                               FROM transaksi_detail
                               WHERE barang_id = ?");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ((int)$data['jumlah'] > 0) {
    header("Location: barang.php?error=dipakai");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM barang WHERE id = ?");

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: barang.php?status=hapus");
    exit;
}

echo "Gagal menghapus barang: " . htmlspecialchars(mysqli_error($conn));
?>