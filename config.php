<?php
// Pengaturan zona waktu
date_default_timezone_set('Asia/Jakarta');

// Koneksi ke database
$host = 'localhost'; // atau sesuaikan dengan host Anda
$user = 'root';      // atau sesuaikan dengan username database Anda
$pass = '';          // atau sesuaikan dengan password database Anda
$db   = 'absensi_penggajian'; // nama database yang Anda buat

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi helper untuk membersihkan input
function sanitize($koneksi, $data)
{
    return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data)));
}

// Mulai sesi
session_start();
