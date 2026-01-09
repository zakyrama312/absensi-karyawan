<?php
$page_title = 'Kelola Jadwal Kerja';
include 'header.php';

$pesan = '';
$error = '';

// 1. Proses Simpan Jadwal Massal
if (isset($_POST['tambah_jadwal'])) {
    $id_karyawan = sanitize($koneksi, $_POST['id_karyawan']);
    $id_shift = sanitize($koneksi, $_POST['id_shift']);
    $tanggal_mulai = sanitize($koneksi, $_POST['tanggal_mulai']);
    $tanggal_selesai = sanitize($koneksi, $_POST['tanggal_selesai']);

    if (strtotime($tanggal_mulai) > strtotime($tanggal_selesai)) {
        $error = "Tanggal mulai tidak boleh lebih besar dari tanggal selesai.";
    } else {
        $begin = new DateTime($tanggal_mulai);
        $end = new DateTime($tanggal_selesai);
        $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $success_count = 0;
        foreach ($period as $dt) {
            $tgl = $dt->format("Y-m-d");
            $cek = mysqli_query($koneksi, "SELECT id FROM jadwal_kerja WHERE id_karyawan='$id_karyawan' AND tanggal='$tgl'");

            if (mysqli_num_rows($cek) > 0) {
                $query = "UPDATE jadwal_kerja SET id_shift='$id_shift' WHERE id_karyawan='$id_karyawan' AND tanggal='$tgl'";
            } else {
                $query = "INSERT INTO jadwal_kerja (id_karyawan, id_shift, tanggal) VALUES ('$id_karyawan', '$id_shift', '$tgl')";
            }
            if (mysqli_query($koneksi, $query)) $success_count++;
        }
        $pesan = "Berhasil memproses $success_count jadwal kerja.";
    }
}

// 2. Proses Hapus Jadwal
if (isset($_GET['hapus'])) {
    $id = sanitize($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM jadwal_kerja WHERE id=$id")) {
        $pesan = "Jadwal berhasil dihapus.";
    }
}

// 3. Query Ambil Data
$query = "SELECT jk.*, k.nama, s.nama_shift, s.jam_masuk, s.jam_keluar 
          FROM jadwal_kerja jk 
          JOIN karyawan k ON jk.id_karyawan = k.id 
          JOIN shifts s ON jk.id_shift = s.id 
          ORDER BY jk.tanggal DESC";
$result = mysqli_query($koneksi, $query);
?>


<style>
    /* Custom Styling Select2 & DataTables */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        padding: 6px;
        border-color: #e2e8f0 !important;
        border-radius: 0.75rem !important;
    }

    /* Mempercantik DataTables agar tidak kaku */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        @apply border border-slate-200 rounded-xl px-4 py-2 text-sm outline-none focus: ring-2 focus:ring-indigo-500 transition-all;
        margin-bottom: 20px;
    }

    table.dataTable.no-footer {
        border-bottom: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        @apply bg-indigo-600 text-white border-none rounded-lg font-bold !important;
    }
</style>

<div class="space-y-6">
    <?php if ($pesan): ?>
        <div
            class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-xl shadow-sm animate-fade-in">
            <p class="text-sm font-medium">✅ <?= $pesan; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center gap-3 mb-8">
            <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">Atur Jadwal Massal</h2>
                <p class="text-xs text-slate-400 font-medium">Tentukan shift untuk periode tertentu sekaligus.</p>
            </div>
        </div>

        <form method="POST" action="" class="bg-slate-50 p-4 rounded">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih
                        Karyawan</label>
                    <select name="id_karyawan" required class="select2-js w-full">
                        <option value="">Cari Nama...</option>
                        <?php
                        $res_k = mysqli_query($koneksi, "SELECT id, nama FROM karyawan ORDER BY nama ASC");
                        while ($k = mysqli_fetch_assoc($res_k)) echo "<option value='{$k['id']}'>{$k['nama']}</option>";
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Shift
                        Kerja</label>
                    <select name="id_shift" required
                        class="w-full rounded-xl border-slate-200 text-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <?php
                        $res_s = mysqli_query($koneksi, "SELECT id, nama_shift FROM shifts");
                        while ($s = mysqli_fetch_assoc($res_s)) echo "<option value='{$s['id']}'>{$s['nama_shift']}</option>";
                        ?>
                    </select>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Mulai</label>
                    <input type="date" name="tanggal_mulai" required
                        class="w-full rounded-xl border-slate-200 text-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Selesai</label>
                    <input type="date" name="tanggal_selesai" required
                        class="w-full rounded-xl border-slate-200 text-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>
            <button type="submit" name="tambah_jadwal"
                class="mt-8 bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-indigo-700 transition shadow-lg shadow-indigo-600/20 active:scale-95">
                <i class="fas fa-save mr-2"></i> Simpan Penjadwalan
            </button>
        </form>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <div class="overflow-x-auto">
            <table id="jadwalTable" class="w-full text-sm text-left border-separate border-spacing-y-3">
                <thead>
                    <tr class="text-[10px] text-slate-400 uppercase tracking-[0.2em]">
                        <th class="pb-4 px-4 font-bold">Karyawan</th>
                        <th class="pb-4 px-4 font-bold text-center">Tanggal</th>
                        <th class="pb-4 px-4 font-bold text-center">Shift</th>
                        <th class="pb-4 px-4 font-bold text-center">Waktu Kerja</th>
                        <th class="pb-4 px-4 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="group hover:bg-slate-50 transition-all duration-200 shadow-sm">
                            <td class="py-4 px-4 bg-white border-y border-l border-slate-50 rounded-l-2xl">
                                <div class="font-bold text-slate-700 tracking-tight"><?= htmlspecialchars($row['nama']); ?>
                                </div>
                            </td>
                            <td class="py-4 px-4 bg-white border-y border-slate-50 ">
                                <div class="text-xs font-bold text-slate-500">
                                    <?= date('d M Y', strtotime($row['tanggal'])); ?></div>
                            </td>
                            <td class="py-4 px-4 bg-white border-y border-slate-50 ">
                                <?php if ($row['id_shift'] == 3) : // OFF 
                                ?>
                                    <span
                                        class="bg-rose-50 text-rose-600 text-[10px] font-bold px-3 py-1 rounded-lg border border-rose-100 uppercase tracking-tighter">OFF
                                        / Libur</span>
                                <?php else : ?>
                                    <span
                                        class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-3 py-1 rounded-lg border border-indigo-100 uppercase tracking-tighter"><?= $row['nama_shift']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 bg-white border-y border-slate-50 ">
                                <?php if ($row['jam_masuk']) : ?>
                                    <div
                                        class="inline-flex items-center gap-2 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                        <i class="far fa-clock text-indigo-500 text-[10px]"></i>
                                        <span class="text-xs font-black text-slate-600 font-mono italic">
                                            <?= date('H:i', strtotime($row['jam_masuk'])); ?> -
                                            <?= date('H:i', strtotime($row['jam_keluar'])); ?>
                                        </span>
                                    </div>
                                <?php else : ?>
                                    <span class="text-[10px] text-red-300 italic font-medium">Tidak ada jam kerja</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 bg-white border-y border-r border-slate-50 rounded-r-2xl ">
                                <a href="kelola_jadwal.php?hapus=<?= $row['id']; ?>"
                                    onclick="return confirm('Hapus jadwal ini?')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>