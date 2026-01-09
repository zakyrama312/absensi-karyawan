<?php
$page_title = 'Riwayat Absensi';
include 'header.php';

// Filter
$filter_tanggal = isset($_GET['tanggal']) ? sanitize($koneksi, $_GET['tanggal']) : '';
$filter_karyawan = isset($_GET['karyawan']) ? sanitize($koneksi, $_GET['karyawan']) : '';

$where_clause = "WHERE 1=1";
if (!empty($filter_tanggal)) {
    $where_clause .= " AND a.tanggal = '$filter_tanggal'";
}
if (!empty($filter_karyawan)) {
    $where_clause .= " AND k.id = '$filter_karyawan'";
}

$query = "SELECT a.*, k.nama, k.username, k.jabatan FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id $where_clause ORDER BY a.tanggal DESC, a.jam_masuk DESC";
$result = mysqli_query($koneksi, $query);
?>

<style>
    /* Gunakan style yang sama agar seragam dengan Kelola Jadwal */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        padding: 6px;
        border-color: #e2e8f0 !important;
        border-radius: 0.75rem !important;
    }

    .dataTables_wrapper .dataTables_length select {
        @apply border rounded-md px-2 py-1 mr-2;
    }

    .dataTables_wrapper .dataTables_filter input {
        @apply border rounded-md px-3 py-1 ml-2 outline-none focus: ring-2 focus:ring-indigo-500;
    }

    .dataTables_wrapper .dataTables_info {
        @apply text-xs text-gray-500 mt-4;
    }

    .dataTables_wrapper .dataTables_paginate {
        @apply mt-4;
    }

    table.dataTable thead th {
        border-bottom: 1px solid #e5e7eb !important;
    }
</style>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-3">
                <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 shadow-sm">
                    <i class="fas fa-clock-rotate-left text-xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Riwayat Absensi Karyawan</h2>
                    <p class="text-xs text-slate-400 font-medium">Pantau kehadiran dan total jam kerja seluruh staff
                        secara real-time.</p>
                </div>
            </div>

            <div class="w-full md:w-auto">
                <a href="export.php?tanggal=<?= $filter_tanggal; ?>&karyawan=<?= $filter_karyawan; ?>"
                    class="flex items-center justify-center gap-2 bg-emerald-500 text-white py-2.5 px-6 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/20 text-sm font-bold active:scale-95">
                    <i class="fas fa-file-excel"></i>
                    <span>Ekspor ke Excel</span>
                </a>
            </div>
        </div>

        <form method="GET" action=""
            class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-slate-50 p-4 rounded-xl border border-slate-100">
            <div>
                <label class="block text-[10px]  font-bold text-slate-400 uppercase tracking-widest mb-1">Filter
                    Tanggal</label>
                <input type="date" name="tanggal" value="<?= $filter_tanggal; ?>"
                    class="block w-full py-2 px-4 rounded-lg border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Filter
                    Karyawan</label>
                <select name="karyawan" required class="select2-js w-full">
                    <option value="">Cari Nama Karyawan...</option>
                    <?php
                    $res_k = mysqli_query($koneksi, "SELECT id, nama FROM karyawan ORDER BY nama ASC");
                    while ($k = mysqli_fetch_assoc($res_k)) {
                        echo "<option value='{$k['id']}'>{$k['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition font-semibold text-sm shadow-md shadow-indigo-600/20">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="riwayat.php"
                    class="bg-white border border-slate-200 text-slate-500 py-2 px-4 rounded-lg hover:bg-slate-100 transition text-sm flex items-center justify-center">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-separate border-spacing-y-2" id="riwayatTable">
                <thead>
                    <tr class="text-[10px] text-slate-400 uppercase tracking-[0.2em]">
                        <th class="pb-4 px-4 font-bold">Identitas Karyawan</th>
                        <th class="pb-4 px-4 font-bold text-center">Tanggal</th>
                        <th class="pb-4 px-4 font-bold text-center">Jam Masuk</th>
                        <th class="pb-4 px-4 font-bold text-center">Jam Keluar</th>
                        <th class="pb-4 px-4 font-bold text-right">Durasi Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) :
                        $total_jam_kerja = '-';
                        if ($row['jam_masuk'] && $row['jam_keluar']) {
                            $jam_masuk = new DateTime($row['jam_masuk']);
                            $jam_keluar = new DateTime($row['jam_keluar']);
                            $durasi = $jam_masuk->diff($jam_keluar);
                            $total_jam_kerja = $durasi->format('%h Jam %i Menit');
                        }
                    ?>
                        <tr class="group hover:bg-slate-50 transition-all duration-200 border-b border-gray-50">
                            <td class="py-4 px-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700"><?= htmlspecialchars($row['nama']); ?></span>
                                    <span
                                        class="text-[10px] text-slate-400 uppercase tracking-tighter">@<?= htmlspecialchars($row['username']); ?>
                                        • <?= $row['jabatan'] ?></span>
                                </div>
                            </td>
                            <td class="py-4 px-4 ">
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    <?= date('d M Y', strtotime($row['tanggal'])); ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 ">
                                <div class="flex items-center gap-2 text-emerald-600 font-bold">
                                    <i class="fas fa-sign-in-alt text-[10px]"></i>
                                    <?= $row['jam_masuk']; ?>
                                </div>
                            </td>
                            <td class="py-4 px-4 ">
                                <div
                                    class="flex items-center gap-2 <?= $row['jam_keluar'] ? 'text-red-600 font-bold' : 'text-slate-300 italic text-xs' ?>">
                                    <i class="fas fa-sign-out-alt text-[10px]"></i>
                                    <?= $row['jam_keluar'] ?? 'Belum Keluar'; ?>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-indigo-600 font-extrabold text-sm">
                                    <?= $total_jam_kerja; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>