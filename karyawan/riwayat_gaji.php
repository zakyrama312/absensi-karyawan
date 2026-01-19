<?php
session_start();
require '../config.php';

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: ../login_karyawan.php");
    exit();
}

$id_karyawan = $_SESSION['id_karyawan'];
$karyawan_nama = $_SESSION['karyawan_nama'];

function getNamaBulan($angka)
{
    $bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return $bulan[$angka];
}

$query = "SELECT * FROM penggajian WHERE id_karyawan = ? ORDER BY tahun DESC, bulan DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_karyawan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Slip Gaji - <?= htmlspecialchars($karyawan_nama); ?></title>
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
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto p-4 sm:p-8">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-600 p-3 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Riwayat Slip Gaji</h1>
                    <p class="text-sm text-slate-400 font-medium">Data gaji bersihmu bulan ini.</p>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="riwayat_karyawan.php"
                    class="bg-indigo-50 text-indigo-600 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-[10px]"></i> Absensi
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
                    <table class="w-full text-sm text-left border-separate border-spacing-y-3" id="gajiTable">
                        <thead>
                            <tr class="text-[10px] text-slate-400 uppercase tracking-widest">
                                <th class="pb-4 px-4 font-bold">Periode</th>
                                <th class="pb-4 px-4 font-bold text-center">Gaji Pokok</th>
                                <th class="pb-4 px-4 font-bold text-center">Tunjangan & Lembur</th>
                                <th class="pb-4 px-4 font-bold text-center">Potongan</th>
                                <th class="pb-4 px-4 font-bold text-center">Total Gaji</th>
                                <th class="pb-4 px-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($data = mysqli_fetch_assoc($result)):
                                $total_allow = $data['subsidi_makan'] + $data['subsidi_transport'] + $data['lembur'];
                                $total_pot = $data['potongan_mangkir_sakit'] + $data['insentif'] + $data['prestasi'] + ($data['jml_izin_keluar'] * 11857);
                            ?>
                            <tr class="group hover:bg-slate-50 transition-all duration-200">
                                <td class="py-5 px-4 bg-white border-y border-l border-slate-50 rounded-l-2xl">
                                    <div class="font-bold text-slate-700">
                                        <?= getNamaBulan($data['bulan']) . " " . $data['tahun']; ?></div>
                                    <div class="text-[9px] text-slate-400">ID Slip:
                                        #<?= str_pad($data['id'], 5, '0', STR_PAD_LEFT); ?></div>
                                </td>

                                <td
                                    class="py-5 px-4 bg-white border-y border-slate-50 text-center font-medium text-slate-500">
                                    Rp <?= number_format($data['total_gaji_pokok'], 0, ',', '.'); ?>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-slate-50 text-center">
                                    <span class="text-emerald-600 font-bold text-xs">+ Rp
                                        <?= number_format($total_allow, 0, ',', '.'); ?></span>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-slate-50 text-center">
                                    <span class="text-rose-500 font-bold text-xs">- Rp
                                        <?= number_format($total_pot, 0, ',', '.'); ?></span>
                                </td>

                                <td class="py-5 px-4 bg-white border-y border-slate-50 text-center">
                                    <div class="text-indigo-600 font-black text-sm">
                                        Rp <?= number_format($data['total_terima'], 0, ',', '.'); ?>
                                    </div>
                                </td>

                                <td
                                    class="py-5 px-4 bg-white border-y border-r border-slate-50 rounded-r-2xl text-right">
                                    <a href="cetak_slip.php?id_karyawan=<?= $id_karyawan ?>&bulan=<?= $data['bulan'] ?>&tahun=<?= $data['tahun'] ?>"
                                        target="_blank"
                                        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-xl text-[10px] font-bold hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all">
                                        <i class="fas fa-file-pdf"></i> Slip
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
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
        $('#gajiTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari data..."
            }
        });
    });
    </script>
</body>

</html>