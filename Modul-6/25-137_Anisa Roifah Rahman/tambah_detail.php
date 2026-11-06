<?php
include "koneksi.php";

$transaksi_id = filter_input(INPUT_GET, 'transaksi_id', FILTER_VALIDATE_INT);

if (!$transaksi_id) {
    header("Location: index.php");
    exit;
}

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barang_id = (int)$_POST['barang_id'];
    $qty = (int)$_POST['qty'];

    if ($barang_id < 1 || $qty < 1) {
        $pesan = "Pilih barang dan masukkan qty minimal 1.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT harga FROM barang WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $barang_id);
        mysqli_stmt_execute($stmt);
        $barang = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$barang) {
            $pesan = "Barang tidak ditemukan.";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO transaksi_detail
                (transaksi_id, barang_id, harga, qty)
                SELECT ?, ?, ?, ?
                WHERE NOT EXISTS (
                    SELECT 1 FROM transaksi_detail
                    WHERE transaksi_id = ? AND barang_id = ?
                )");

            $harga = (int)$barang['harga'];

            mysqli_stmt_bind_param(
                $stmt, "iiiiii",
                $transaksi_id, $barang_id, $harga, $qty,
                $transaksi_id, $barang_id
            );

            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $stmt = mysqli_prepare($conn, "UPDATE transaksi
                    SET total = (
                        SELECT COALESCE(SUM(harga * qty), 0)
                        FROM transaksi_detail
                        WHERE transaksi_id = ?
                    )
                    WHERE id = ?");

                mysqli_stmt_bind_param($stmt, "ii", $transaksi_id, $transaksi_id);
                mysqli_stmt_execute($stmt);

                header("Location: detail.php?id=" . $transaksi_id);
                exit;
            } else {
                $pesan = "Barang sudah ada dalam transaksi.";
            }
        }
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM barang b
    WHERE NOT EXISTS (
        SELECT 1 FROM transaksi_detail td
        WHERE td.transaksi_id = ? AND td.barang_id = b.id
    )
    ORDER BY nama_barang");

mysqli_stmt_bind_param($stmt, "i", $transaksi_id);
mysqli_stmt_execute($stmt);
$barangList = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Detail</title><link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container form-box">

<h2>Tambah Detail Transaksi #<?= $transaksi_id ?></h2>

<?php if ($pesan != "") { ?>
<div class="notice error">
    <?= htmlspecialchars($pesan) ?>
</div>
<?php } ?>

<form method="POST">
    <label>Barang</label>

    <select name="barang_id" required>
    <option value="">-- Pilih Barang --</option>

    <?php while ($b = mysqli_fetch_assoc($barangList)) { ?>
    <option value="<?= (int)$b['id'] ?>">
        <?= htmlspecialchars($b['kode_barang'] . " - " . $b['nama_barang']) ?>
        (Rp <?= number_format($b['harga'], 0, ',', '.') ?>)
    </option>
    <?php } ?>
    </select>

    <label>Qty</label>
    <input type="number" name="qty" min="1" required>

    <button class="btn success" type="submit">Simpan Detail</button>

    <a class="btn secondary" href="detail.php?id=<?= $transaksi_id ?>">Batal</a>
</form>

    <p class="small">Barang yang sudah ditambahkan tidak ditampilkan kembali.</p>

</div>
</body>
</html>