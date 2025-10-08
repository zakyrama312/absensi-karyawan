<?php
// File ini digunakan untuk membuat hash password baru untuk admin.
// Setelah digunakan, file ini sebaiknya dihapus dari server Anda demi keamanan.

// Ganti 'admin123' dengan password baru yang Anda inginkan.
$password_baru = 'admin123';

// Membuat hash dari password menggunakan algoritma default PHP (sangat aman).
$hash_password = password_hash($password_baru, PASSWORD_DEFAULT);

// Tampilkan hash yang sudah jadi.
// Salin (copy) seluruh teks yang muncul di layar.
echo "<h1>Password Hash Baru Anda:</h1>";
echo "<p>Silakan salin teks di bawah ini dan masukkan ke database Anda.</p>";
echo "<hr>";
echo "<p style='font-family: monospace; font-size: 16px; background-color: #f0f0f0; padding: 10px; border: 1px solid #ccc; border-radius: 5px;'>" . $hash_password . "</p>";
