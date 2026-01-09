<?php
$page_title = 'Kelola Izin Keluar';
include 'header.php';

// Proses Simpan Izin Baru (Jam Pergi)
if (isset($_POST['simpan_izin'])) {
    $id_k = sanitize($koneksi, $_POST['id_karyawan']);
    $tgl  = sanitize($koneksi, $_POST['tanggal']);
    $jam  = sanitize($koneksi, $_POST['jam_pergi']);
    $ket  = sanitize($koneksi, $_POST['keperluan']);

    $query = "INSERT INTO izin_keluar (id_karyawan, tanggal, jam_pergi, keperluan, status_izin) 
              VALUES ('$id_k', '$tgl', '$jam', '$ket', 'proses')";
    mysqli_query($koneksi, $query);
    echo "<script>alert('Izin Keluar Berhasil Dicatat!'); window.location='izin_keluar.php';</script>";
}

// Proses Update Jam Kembali
if (isset($_GET['selesai'])) {
    $id_izin = sanitize($koneksi, $_GET['selesai']);
    $jam_sekarang = date('H:i:s');

    $query = "UPDATE izin_keluar SET jam_kembali = '$jam_sekarang', status_izin = 'kembali' WHERE id = '$id_izin'";
    mysqli_query($koneksi, $query);
    echo "<script>alert('Karyawan Telah Kembali!'); window.location='izin_keluar.php';</script>";
}

// Ambil Data Izin Hari Ini
$tgl_hari_ini = date('Y-m-d');
$sql_izin = "SELECT i.*, k.nama FROM izin_keluar i JOIN karyawan k ON i.id_karyawan = k.id WHERE i.tanggal = '$tgl_hari_ini' ORDER BY i.id DESC";
$res_izin = mysqli_query($koneksi, $sql_izin);
?>
<style>
    /* Gunakan style yang sama agar seragam dengan Kelola Jadwal */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        padding: 6px;
        border-color: #e2e8f0 !important;
        border-radius: 0.75rem !important;
    }
</style>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-600">
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 shadow-sm">
                <i class="fas fa-file-signature text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Input Izin Keluar</h2>
                <p class="text-xs text-slate-400 font-medium">Catat detail keberangkatan karyawan yang memerlukan izin
                    selama jam kerja berlangsung.</p>
            </div>
        </div>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Pilih Karyawan</label>
                <select name="id_karyawan" required class="select2-js w-full">
                    <option value="">Cari Nama Karyawan...</option>
                    <?php
                    $res_k = mysqli_query($koneksi, "SELECT id, nama FROM karyawan ORDER BY nama ASC");
                    while ($k = mysqli_fetch_assoc($res_k)) {
                        echo "<option value='{$k['id']}'>{$k['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Tanggal</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" class="w-full border p-2 rounded-md"
                    required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Jam Pergi</label>
                <input type="time" name="jam_pergi" value="<?= date('H:i'); ?>" class="w-full border p-2 rounded-md"
                    required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Keperluan</label>
                <textarea name="keperluan" rows="2" class="w-full border p-2 rounded-md"
                    placeholder="Contoh: Urusan Keluarga"></textarea>
            </div>
            <button type="submit" name="simpan_izin"
                class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                Catat Jam Pergi
            </button>
        </form>
    </div>

    <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 shadow-sm">
                <i class="fas fa-stopwatch text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Monitoring Izin (Hari Ini)</h2>
                <p class="text-xs text-slate-400 font-medium">Pantau durasi dan status karyawan yang sedang izin keluar
                    kantor secara real-time.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="monitoringTable">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="py-3 px-4">Nama</th>
                        <th class="py-3 px-4">Keperluan</th>
                        <th class="py-3 px-4 text-center">Pergi</th>
                        <th class="py-3 px-4 text-center">Kembali</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">

                    <?php while ($iz = mysqli_fetch_assoc($res_izin)) : ?>
                        <tr>
                            <td class="py-3 px-4 font-bold text-gray-700"><?= $iz['nama']; ?></td>
                            <td class="py-3 px-4 text-gray-500 italic"><?= $iz['keperluan']; ?></td>
                            <td class="py-3 px-4 text-center font-mono"><?= $iz['jam_pergi']; ?></td>
                            <td class="py-3 px-4 text-center font-mono">
                                <?= $iz['jam_kembali'] ?? '<span class="text-red-500 animate-pulse">Belum Kembali</span>'; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($iz['status_izin'] == 'proses') : ?>
                                    <a href="izin_keluar.php?selesai=<?= $iz['id']; ?>"
                                        onclick="return confirm('Karyawan ini sudah kembali?')"
                                        class="bg-green-500 text-white px-3 py-1 rounded-full text-xs hover:bg-green-600">
                                        Update Kembali
                                    </a>
                                <?php else : ?>
                                    <span class="text-green-600 font-bold">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>