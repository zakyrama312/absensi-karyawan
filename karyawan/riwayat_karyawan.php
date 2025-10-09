<?php
session_start();
require '../config.php';

// Wajib login untuk akses halaman ini
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: ../login_karyawan.php");
    exit();
}

$id_karyawan = $_SESSION['id_karyawan'];
$karyawan_nama = $_SESSION['karyawan_nama'];

// =================================================================
// FUNGSI BANTUAN UNTUK FORMAT TANGGAL KE BAHASA INDONESIA
// =================================================================
function formatTanggalIndonesia($tanggal)
{
    // Pastikan ekstensi intl diaktifkan di server Anda (biasanya sudah aktif)
    $formatter = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        'Asia/Jakarta',
        IntlDateFormatter::GREGORIAN,
        'd MMMM yyyy'
    );
    return $formatter->format(strtotime($tanggal));
}
// =================================================================


// Ambil data absensi menggunakan loop while
$query = "SELECT tanggal, jam_masuk, jam_keluar FROM absensi WHERE id_karyawan = ? ORDER BY tanggal desc";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$riwayat = [];
while ($baris = mysqli_fetch_assoc($result)) {
    $riwayat[] = $baris;
}
mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi - <?php echo htmlspecialchars($karyawan_nama); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4 sm:p-6 lg:p-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Riwayat Absensi Anda</h1>
                    <p class="text-gray-600">Selamat datang, <?php echo htmlspecialchars($karyawan_nama); ?>!</p>
                </div>
                <a href="logout_karyawan.php"
                    class="mt-4 sm:mt-0 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Logout
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="riwayatTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Keluar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Jam Kerja</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada data absensi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $data): ?>
                                <tr>
                                    <!-- PANGGIL FUNGSI BARU DI SINI -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo formatTanggalIndonesia($data['tanggal']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo $data['jam_masuk'] ? date('H:i:s', strtotime($data['jam_masuk'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo $data['jam_keluar'] ? date('H:i:s', strtotime($data['jam_keluar'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        if ($data['jam_masuk'] && $data['jam_keluar']) {
                                            $masuk = new DateTime($data['jam_masuk']);
                                            $keluar = new DateTime($data['jam_keluar']);
                                            $interval = $masuk->diff($keluar);
                                            echo $interval->format('%h jam %i menit');
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-6">
                <!-- <a href="../index.php" class="text-sm text-indigo-600 hover:text-indigo-500">Kembali ke Halaman
                    Absen</a> -->
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script>
        new DataTable('#riwayatTable', {
            responsive: true
        });
    </script>
</body>

</html>