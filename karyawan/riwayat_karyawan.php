<?php
session_start();
require '../config.php';

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: ../login_karyawan.php");
    exit();
}

$id_karyawan = $_SESSION['id_karyawan'];
$karyawan_nama = $_SESSION['karyawan_nama'];

function formatTanggalIndonesia($tanggal)
{
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
// Gunakan DISTINCT untuk mencegah duplikasi baris yang identik
$query = "SELECT DISTINCT a.tanggal, a.jam_masuk, a.jam_keluar, s.nama_shift, s.id as id_shift
          FROM absensi a 
          LEFT JOIN jadwal_kerja jk ON a.id_jadwal = jk.id 
          LEFT JOIN shifts s ON jk.id_shift = s.id 
          WHERE a.id_karyawan = ? 
          ORDER BY a.tanggal DESC, a.jam_masuk ASC";
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
    <title>Riwayat Absensi - <?= htmlspecialchars($karyawan_nama); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .dataTables_wrapper .dataTables_filter input {
        @apply border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus: ring-2 focus:ring-indigo-500 transition-all mb-4;
    }

    table.dataTable.no-footer {
        border-bottom: none !important;
    }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto p-4 sm:p-8">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 p-3 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Riwayat Absensi</h1>
                    <p class="text-sm text-slate-400 font-medium">Halo, <span
                            class="text-indigo-600 font-bold"><?= htmlspecialchars($karyawan_nama); ?></span>! Cek
                        kehadiranmu di sini.</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="riwayat_gaji.php"
                    class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar"></i> Lihat Gaji
                </a>
                <a href="logout_karyawan.php"
                    class="bg-rose-50 text-rose-600 px-4 py-2.5 rounded-xl font-bold text-sm hover:bg-rose-600 hover:text-white transition-all border border-rose-100">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-separate border-spacing-y-3" id="riwayatTable">
                        <thead>
                            <tr class="text-[10px] text-slate-400 uppercase tracking-widest">
                                <th class="pb-4 px-4 font-bold">Tanggal</th>
                                <th class="pb-4 px-4 font-bold text-center">Shift Kerja</th>
                                <th class="pb-4 px-4 font-bold text-center">Jam Masuk</th>
                                <th class="pb-4 px-4 font-bold text-center">Jam Keluar</th>
                                <th class="pb-4 px-4 font-bold text-right">Durasi Kerja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riwayat as $data): ?>
                            <tr class="group hover:bg-slate-50 transition-all duration-200 shadow-sm">
                                <td class="py-5 px-4 bg-white border-y border-l border-slate-50 rounded-l-2xl">
                                    <div class="font-bold text-slate-700">
                                        <?= formatTanggalIndonesia($data['tanggal']); ?></div>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-slate-50 ">
                                    <?php if ($data['id_shift'] == 3 || empty($data['nama_shift'])) : ?>
                                    <span
                                        class="bg-rose-50 text-rose-600 text-[10px] font-bold px-3 py-1 rounded-lg border border-rose-100 uppercase tracking-tighter">
                                        <i class="fas fa-mug-hot mr-1"></i> OFF / Libur
                                    </span>
                                    <?php else : ?>
                                    <span
                                        class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-3 py-1 rounded-lg border border-indigo-100 uppercase tracking-tighter">
                                        <?= $data['nama_shift']; ?>
                                    </span>
                                    <?php endif; ?>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-slate-50 ">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 font-mono font-bold text-xs border border-emerald-100">
                                        <i class="fas fa-sign-in-alt text-[10px]"></i>
                                        <?= $data['jam_masuk'] ? date('H:i', strtotime($data['jam_masuk'])) : '--:--'; ?>
                                    </span>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-slate-50 ">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg <?= $data['jam_keluar'] ? 'bg-red-50 text-red-600 border-red-100' : 'bg-slate-50 text-slate-300 border-slate-100' ?> font-mono font-bold text-xs border">
                                        <i class="fas fa-sign-out-alt text-[10px]"></i>
                                        <?= $data['jam_keluar'] ? date('H:i', strtotime($data['jam_keluar'])) : '--:--'; ?>
                                    </span>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-r border-slate-50 rounded-r-2xl">
                                    <div class="text-xs font-black text-slate-600 italic">
                                        <?php
                                            if ($data['jam_masuk'] && $data['jam_keluar']) {
                                                $masuk = new DateTime($data['jam_masuk']);
                                                $keluar = new DateTime($data['jam_keluar']);
                                                $interval = $masuk->diff($keluar);
                                                echo $interval->format('%h Jam %i Menit');
                                            } else {
                                                echo '<span class="text-slate-300 font-medium">--</span>';
                                            }
                                            ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- <p class="text-center mt-8 text-xs text-slate-400 font-medium tracking-wide uppercase">
            &copy; 2026 Ventera • SMK Negeri 1 Slawi
        </p> -->
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#riwayatTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari tanggal...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_",
                paginate: {
                    next: '<i class="fas fa-chevron-right"></i>',
                    previous: '<i class="fas fa-chevron-left"></i>'
                }
            }
        });
    });
    </script>
</body>

</html>