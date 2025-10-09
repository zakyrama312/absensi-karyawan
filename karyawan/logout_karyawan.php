<?php
session_start();

// Hapus sesi spesifik untuk karyawan
unset($_SESSION['id_karyawan']);
unset($_SESSION['karyawan_nama']);

// Hancurkan sesi jika perlu (opsional, tapi disarankan)
// session_destroy();

// Redirect ke halaman login karyawan
header("Location: ../login_karyawan.php");
exit();
