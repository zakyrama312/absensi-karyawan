<?php
$page_title = 'Kelola Data Absensi';
include 'header.php';

$pesan = '';
$error = '';

// --- LOGIKA PROSES (Tetap Sama) ---
if (isset($_POST['simpan'])) {
    $id = sanitize($koneksi, $_POST['id']);
    $jam_masuk = sanitize($koneksi, $_POST['jam_masuk']);
    $jam_keluar = sanitize($koneksi, $_POST['jam_keluar']);

    $valid = true;
    if (!empty($jam_masuk) && !preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $jam_masuk)) {
        $error = "Format jam masuk salah (HH:MM:SS).";
        $valid = false;
    }
    if (!empty($jam_keluar) && !preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/", $jam_keluar)) {
        $error = "Format jam keluar salah (HH:MM:SS).";
        $valid = false;
    }

    if ($valid) {
        $jam_masuk_sql = empty($jam_masuk) ? "NULL" : "'$jam_masuk'";
        $jam_keluar_sql = empty($jam_keluar) ? "NULL" : "'$jam_keluar'";
        $status = 'tidak_hadir';
        if (!empty($jam_masuk) && empty($jam_keluar)) {
            $status = 'masuk';
        } else if (!empty($jam_masuk) && !empty($jam_keluar)) {
            $status = 'keluar';
        }

        $query_update = "UPDATE absensi SET jam_masuk = $jam_masuk_sql, jam_keluar = $jam_keluar_sql, status = '$status' WHERE id=$id";
        if (mysqli_query($koneksi, $query_update)) {
            $pesan = "Data absensi berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui data absensi.";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = sanitize($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM absensi WHERE id=$id")) {
        $pesan = "Data absensi berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data.";
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = sanitize($koneksi, $_GET['edit']);
    $query_edit = "SELECT a.*, k.nama, s.nama_shift, s.jam_masuk as shift_masuk, s.jam_keluar as shift_keluar FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id LEFT JOIN jadwal_kerja jk ON a.id_jadwal = jk.id LEFT JOIN shifts s ON jk.id_shift = s.id WHERE a.id=$id";
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, $query_edit));
}

$filter_tanggal = isset($_GET['tanggal']) ? sanitize($koneksi, $_GET['tanggal']) : '';
$filter_karyawan = isset($_GET['karyawan']) ? sanitize($koneksi, $_GET['karyawan']) : '';
$where_clause = "WHERE 1=1";
if (!empty($filter_tanggal)) {
    $where_clause .= " AND a.tanggal = '$filter_tanggal'";
}
if (!empty($filter_karyawan)) {
    $where_clause .= " AND k.id = '$filter_karyawan'";
}

$query = "SELECT a.*, k.nama, k.username, s.nama_shift FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id LEFT JOIN jadwal_kerja jk ON a.id_jadwal = jk.id LEFT JOIN shifts s ON jk.id_shift = s.id $where_clause ORDER BY a.tanggal DESC, a.jam_masuk DESC";
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
</style>
<div class="space-y-6">

    <?php if ($pesan): ?>
        <div
            class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-xl shadow-sm animate-fade-in-down">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <p class="text-sm font-medium"><?= $pesan; ?></p>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r-xl shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-rose-500"></i>
                <p class="text-sm font-medium"><?= $error; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($edit_data): ?>
        <div class="bg-white p-6 rounded-2xl shadow-xl border border-indigo-100 ring-2 ring-indigo-500 ring-opacity-5 mb-8">

            <div
                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 border-b border-slate-50 pb-6">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600">
                        <i class="fas fa-user-edit text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 tracking-tight">Edit Absensi</h2>
                        <p class="text-xs text-slate-400 font-medium">
                            <?= htmlspecialchars($edit_data['nama']); ?> •
                            <?= date('d M Y', strtotime($edit_data['tanggal'])); ?>
                        </p>
                    </div>
                </div>

                <div
                    class="w-full md:w-80 bg-indigo-50 border border-indigo-100 px-6 py-3 rounded-xl shadow-sm text-center md:text-right">
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Shift Terjadwal</p>
                    <p class="text-sm font-extrabold text-indigo-700 uppercase">
                        <?= $edit_data['nama_shift'] ?? 'Tanpa Jadwal'; ?></p>
                    <div
                        class="flex items-center justify-center md:justify-end gap-2 mt-1 text-indigo-500 font-mono text-xs">
                        <i class="far fa-clock text-[10px]"></i>
                        <span><?= $edit_data['shift_masuk'] ?? '--'; ?> - <?= $edit_data['shift_keluar'] ?? '--'; ?></span>
                    </div>
                </div>
            </div>

            <form method="POST" action="kelola_absen.php">
                <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Jam Masuk
                            Aktual</label>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-sign-in-alt text-xs"></i>
                            </span>
                            <input type="text" name="jam_masuk" value="<?= $edit_data['jam_masuk'] ?? ''; ?>"
                                class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                placeholder="00:00:00">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Jam Keluar
                            Aktual</label>
                        <div class="relative group">
                            <span
                                class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-sign-out-alt text-xs"></i>
                            </span>
                            <input type="text" name="jam_keluar" value="<?= $edit_data['jam_keluar'] ?? ''; ?>"
                                class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                placeholder="00:00:00">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end items-center gap-3 border-t border-slate-50 pt-6">
                    <a href="kelola_absen.php"
                        class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition font-semibold text-sm">
                        Batal
                    </a>
                    <button type="submit" name="simpan"
                        class="px-8 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition font-bold text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-slate-50/50">
            <div class="flex items-center gap-3 mb-8">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Kelola Data Absensi</h2>
                    <p class="text-xs text-slate-400 font-medium">Lakukan penyesuaian jam masuk dan keluar karyawan jika
                        terdapat kekeliruan data absensi.</p>
                </div>
            </div>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Filter
                        Tanggal</label>
                    <input type="date" name="tanggal" value="<?= $filter_tanggal; ?>"
                        class="w-full py-2 px-4  rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih
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
                        class="flex-1 bg-indigo-600 text-white py-2 px-4 rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition text-sm font-bold">Filter</button>
                    <a href="kelola_absen.php"
                        class="bg-white border border-slate-200 text-slate-500 py-2 px-4 rounded-xl hover:bg-slate-100 transition flex items-center justify-center"><i
                            class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-separate border-spacing-y-2" id="absenTable">
                    <thead>
                        <tr class="text-[10px] text-slate-400 uppercase tracking-[0.2em]">
                            <th class="pb-4 px-4 font-bold">Karyawan</th>
                            <th class="pb-4 px-4 font-bold text-center">Shift</th>
                            <th class="pb-4 px-4 font-bold text-center">Tanggal</th>
                            <th class="pb-4 px-4 font-bold text-center">Masuk</th>
                            <th class="pb-4 px-4 font-bold text-center">Keluar</th>
                            <th class="pb-4 px-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr class="group hover:bg-slate-50 transition-all duration-200 border-b border-gray-50">
                                <td class="py-4 px-4">
                                    <span class="font-bold text-slate-700"><?= htmlspecialchars($row['nama']); ?></span>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if ($row['nama_shift']): ?>
                                        <span
                                            class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-3 py-1 rounded-lg uppercase border border-indigo-100">
                                            <?= $row['nama_shift']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="text-slate-300 text-[10px] italic font-medium uppercase tracking-tighter">No
                                            Schedule</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-slate-500">
                                    <?= date('d M Y', strtotime($row['tanggal'])); ?>
                                </td>
                                <td class="py-4 px-4">
                                    <span
                                        class="font-mono text-xs px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md font-bold">
                                        <?= $row['jam_masuk'] ?? '--:--'; ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <span
                                        class="font-mono text-xs px-2 py-1 <?= $row['jam_keluar'] ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400' ?> rounded-md font-bold">
                                        <?= $row['jam_keluar'] ?? '--:--'; ?>
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex gap-2">
                                        <a href="kelola_absen.php?edit=<?= $row['id']; ?>"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <a href="kelola_absen.php?hapus=<?= $row['id']; ?>"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                                            <i class="fas fa-trash text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>