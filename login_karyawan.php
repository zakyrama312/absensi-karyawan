<?php
session_start();
require 'config.php';

// Jika karyawan sudah login, redirect ke halaman riwayat
if (isset($_SESSION['id_karyawan'])) {
    header("Location: karyawan/riwayat_karyawan.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($koneksi, $_POST['username']);
    $password = sanitize($koneksi, $_POST['password']);

    // Menggunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($koneksi, "SELECT id, username, nama, password FROM karyawan WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $karyawan = mysqli_fetch_assoc($result);
        // Verifikasi password
        if (password_verify($password, $karyawan['password'])) {
            // Simpan data sesi khusus untuk karyawan
            $_SESSION['id_karyawan'] = $karyawan['id'];
            $_SESSION['karyawan_nama'] = $karyawan['nama'];
            header("Location: karyawan/riwayat_karyawan.php");
            exit();
        } else {
            $error = "username atau password yang Anda masukkan salah.";
        }
    } else {
        $error = "username atau password yang Anda masukkan salah.";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-sm p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login Karyawan</h2>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input type="text" name="username" id="username" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Login
                </button>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="index.php" class="text-sm text-indigo-600 hover:text-indigo-500">Kembali ke Halaman Absen</a>
        </div>
    </div>
</body>

</html>