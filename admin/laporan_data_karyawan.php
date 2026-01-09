<?php
$page_title = 'Laporan Data Karyawan';
include 'header.php';

$query = "SELECT * FROM karyawan ORDER BY nama ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="space-y-6">


    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class=" flex justify-between items-center">
            <div class="flex items-center gap-3 mb-8">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Laporan Data Karyawan</h2>
                    <p class="text-xs text-slate-400 font-medium">Daftar lengkap seluruh karyawan yang terdaftar dalam
                        sistem.</p>
                </div>
            </div>
            <button onclick="exportTableToExcel('tabelKaryawan', 'Laporan_Data_Karyawan')"
                class="bg-emerald-600 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
        <div class="overflow-x-auto">
            <table id="tabelLaporanKaryawan" class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="pb-4 px-4">No</th>
                        <th class="pb-4 px-4">Nama Karyawan</th>
                        <th class="pb-4 px-4">Username</th>
                        <th class="pb-4 px-4">Jabatan</th>
                        <th class="pb-4 px-4">Bagian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-4 px-4"><?= $no++; ?></td>
                            <td class="py-4 px-4 font-bold text-slate-700"><?= $row['nama']; ?></td>
                            <td class="py-4 px-4 text-slate-500"><?= $row['username']; ?></td>
                            <td class="py-4 px-4">
                                <span
                                    class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded"><?= $row['jabatan'] ?></span>
                            </td>
                            <td class="py-4 px-4">
                                <span
                                    class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded"><?= $row['bagian'] ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>