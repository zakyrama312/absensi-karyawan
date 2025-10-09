<?php
require 'config.php';

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($koneksi, $_POST['username']);
    $password = sanitize($koneksi, $_POST['password']);
    $tipe = sanitize($koneksi, $_POST['tipe']);

    // Cek username dan password karyawan
    $query_karyawan = "SELECT id, nama FROM karyawan WHERE username = '$username'";
    $result_karyawan = mysqli_query($koneksi, $query_karyawan);

    if (mysqli_num_rows($result_karyawan) > 0) {
        // NOTE: Untuk kemudahan, kita tidak melakukan verifikasi password di sini.
        // Dalam aplikasi nyata, Anda HARUS memverifikasi password yang di-hash.
        // Contoh: if(password_verify($password, $hash_dari_db)) { ... }

        $karyawan = mysqli_fetch_assoc($result_karyawan);
        $id_karyawan = $karyawan['id'];
        $nama_karyawan = $karyawan['nama'];
        $tanggal = date('Y-m-d');
        $jam = date('H:i:s');

        // Cek apakah sudah absen masuk hari ini
        $query_cek_absen = "SELECT * FROM absensi WHERE id_karyawan = '$id_karyawan' AND tanggal = '$tanggal'";
        $result_cek_absen = mysqli_query($koneksi, $query_cek_absen);
        $data_absen_hari_ini = mysqli_fetch_assoc($result_cek_absen);

        if ($tipe == 'masuk') {
            if ($data_absen_hari_ini) {
                $error = "Anda sudah melakukan absen masuk hari ini.";
            } else {
                $query_insert = "INSERT INTO absensi (id_karyawan, tanggal, jam_masuk, status) VALUES ('$id_karyawan', '$tanggal', '$jam', 'masuk')";
                if (mysqli_query($koneksi, $query_insert)) {
                    $pesan = "Terima kasih, $nama_karyawan. Absen masuk berhasil pada jam $jam.";
                } else {
                    $error = "Gagal melakukan absen masuk.";
                }
            }
        } elseif ($tipe == 'keluar') {
            if (!$data_absen_hari_ini) {
                $error = "Anda belum melakukan absen masuk hari ini.";
            } elseif ($data_absen_hari_ini['jam_keluar'] != NULL) {
                $error = "Anda sudah melakukan absen keluar hari ini.";
            } else {
                $id_absensi = $data_absen_hari_ini['id'];
                $query_update = "UPDATE absensi SET jam_keluar = '$jam', status = 'keluar' WHERE id = '$id_absensi'";
                if (mysqli_query($koneksi, $query_update)) {
                    $pesan = "Terima kasih, $nama_karyawan. Absen keluar berhasil pada jam $jam.";
                } else {
                    $error = "Gagal melakukan absen keluar.";
                }
            }
        }
    } else {
        $error = "username atau Password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <div class="text-center">
            <h1 class="text-2xl font-bold" id="jam"></h1>
            <p class="text-gray-500" id="tanggal"></p>
        </div>
        <h2 class="text-xl font-bold text-center text-gray-700">Silakan Lakukan Absensi</h2>

        <?php if ($pesan): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p><?php echo $pesan; ?></p>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex space-x-4">
                <button type="submit" name="tipe" value="masuk"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Absen Masuk
                </button>
                <button type="submit" name="tipe" value="keluar"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Absen Keluar
                </button>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="admin/" class="text-sm text-indigo-600 hover:text-indigo-500">Login sebagai Admin</a>
        </div>
        <!-- TAMBAHKAN BAGIAN INI -->
        <div class="text-center mt-4">
            <a href="login_karyawan.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                Sudah punya akun? Login untuk lihat riwayat absensi Anda
            </a>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const jam = String(now.getHours()).padStart(2, '0');
            const menit = String(now.getMinutes()).padStart(2, '0');
            const detik = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('jam').textContent = `${jam}:${menit}:${detik}`;

            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('tanggal').textContent = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>