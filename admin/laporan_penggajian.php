<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Laporan Penggajian';
include 'header.php';

// Ambil filter dari URL, default ke bulan & tahun sekarang
$filter_bulan = $_GET['bulan'] ?? date('n');
$filter_tahun = $_GET['tahun'] ?? date('Y');

// Query disesuaikan dengan gambar struktur tabelmu
$query = "SELECT p.*, k.nama 
          FROM penggajian p 
          JOIN karyawan k ON p.id_karyawan = k.id 
          WHERE p.bulan = '$filter_bulan' AND p.tahun = '$filter_tahun'
          ORDER BY k.nama ASC";

$result = mysqli_query($koneksi, $query);
?>

<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                <i class="fas fa-wallet"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Laporan Penggajian Karyawan</h2>
                <p class="text-xs text-slate-400 font-medium">Rekapitulasi total gaji, insentif, dan potongan
                    berdasarkan periode bulan.</p>
            </div>
        </div>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Pilih Bulan</label>
                <select name="bulan" class="w-full rounded-xl border-slate-200 text-sm p-2">
                    <?php
                    $nama_bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = ($i == $filter_bulan) ? 'selected' : '';
                        echo "<option value='$i' $selected>$nama_bulan[$i]</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Tahun</label>
                <input type="number" name="tahun" value="<?= $filter_tahun ?>"
                    class="w-full rounded-xl border-slate-200 text-sm p-2">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 bg-indigo-600 text-white py-2 rounded-xl text-sm font-bold hover:bg-indigo-700 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <button type="button"
                    onclick="exportTableToExcel('tabelGaji', 'Laporan_Gaji_<?= $filter_bulan ?>_<?= $filter_tahun ?>')"
                    class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-emerald-700 transition">
                    <i class="fas fa-file-excel"></i> Export
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="overflow-x-auto">
            <table id="tabelLaporanGaji" class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="pb-4 px-4">Nama Karyawan</th>
                        <th class="pb-4 px-4 text-center">Periode</th>
                        <th class="pb-4 px-4 text-right">Gaji Pokok</th>
                        <th class="pb-4 px-4 text-right">Lembur/Insentif</th>
                        <th class="pb-4 px-4 text-right">Total Terima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-4 font-bold text-slate-700"><?= htmlspecialchars($row['nama']); ?></td>
                                <td class="py-4 px-4 text-slate-500">
                                    <?= $nama_bulan[$row['bulan']] ?> <?= $row['tahun'] ?>
                                </td>
                                <td class="py-4 px-4">Rp <?= number_format($row['total_gaji_pokok'], 0, ',', '.'); ?>
                                </td>
                                <td class="py-4 px-4 text-emerald-600">
                                    + Rp <?= number_format($row['insentif'] + $row['lembur'], 0, ',', '.'); ?>
                                </td>
                                <td class="py-4 px-4 font-mono font-black text-indigo-600">
                                    Rp <?= number_format($row['total_terima'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 italic">Data gaji untuk periode ini
                                belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>