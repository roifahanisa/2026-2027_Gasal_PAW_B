<?php
$conn = mysqli_connect("localhost", "root", "", "penjualan");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
