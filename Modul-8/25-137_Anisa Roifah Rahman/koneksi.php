<?php
session_name('penjualan_simple'); // nama khusus agar tidak bentrok dengan aplikasi lain di localhost
session_start();
mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('localhost', 'root', '', 'penjualan');
if (!$conn) {
    die('Koneksi database gagal');
}

// membersihkan input agar aman dari SQL injection
function esc($s)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($s));
}

// menampilkan teks dengan aman di HTML
function h($s)
{
    return htmlspecialchars($s ?? '');
}

function rp($n)
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
