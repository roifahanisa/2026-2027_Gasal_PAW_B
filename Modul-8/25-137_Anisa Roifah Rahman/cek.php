<?php
// Proteksi halaman: belum login -> ke halaman login
if (!isset($_SESSION['id_user'], $_SESSION['nama'], $_SESSION['level'])) {
    header('Location: login.php');
    exit;
}
// Halaman Data Master hanya untuk level 1 (owner)
if (isset($owner_only) && $_SESSION['level'] != 1) {
    header('Location: index.php');
    exit;
}
