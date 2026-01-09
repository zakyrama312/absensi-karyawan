<?php
$page_title = 'Dashboard Overview';
include 'header.php';

// --- Ambil Data Statistik ---
$tanggal_hari_ini = date('Y-m-d');

// 1. Total Karyawan
$total_karyawan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id) as total FROM karyawan"))['total'];

// 2. Hadir Hari Ini
$hadir_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT id_karyawan) as total FROM absensi WHERE tanggal = '$tanggal_hari_ini'"))['total'];

// 3. Belum Absen Pulang
$belum_pulang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id) as total FROM absensi WHERE tanggal = '$tanggal_hari_ini' AND jam_keluar IS NULL"))['total'];

// 4. Sedang Izin Keluar (Fitur Baru)
$sedang_izin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(id) as total FROM izin_keluar WHERE tanggal = '$tanggal_hari_ini' AND status_izin = 'proses'"))['total'];

$tidak_hadir = $total_karyawan - $hadir_hari_ini;

// --- Data Grafik (7 Hari Terakhir) ---
$labels = [];
$data_kehadiran = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($tgl));
    $val = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(DISTINCT id_karyawan) as total FROM absensi WHERE tanggal = '$tgl'"))['total'];
    $data_kehadiran[] = $val;
}
?>

<div class="flex items-center gap-3 mb-8">
    <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 shadow-sm">
        <i class="fas fa-th-large text-xl"></i>
    </div>
    <div>
        <h2 class="text-lg font-bold text-slate-800 tracking-tight">Overview Dashboard</h2>
        <p class="text-xs text-slate-400 font-medium">Ringkasan statistik kehadiran dan aktivitas karyawan hari ini.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div
        class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group hover:border-indigo-500 transition-all duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Staff</p>
                <h3 class="text-2xl font-black text-slate-800"><?= $total_karyawan; ?></h3>
            </div>
            <div
                class="bg-slate-50 p-3 rounded-xl text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
        <div
            class="mt-4 flex items-center text-[10px] font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg w-fit">
            <i class="fas fa-check-circle mr-1"></i> Database Terupdate
        </div>
    </div>

    <div
        class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group hover:border-emerald-500 transition-all duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hadir Hari Ini</p>
                <h3 class="text-2xl font-black text-slate-800"><?= $hadir_hari_ini; ?></h3>
            </div>
            <div
                class="bg-slate-50 p-3 rounded-xl text-slate-400 group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                <i class="fas fa-user-check text-lg"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-slate-400">
            Mangkir/Sakit: <span class="text-rose-500 ml-1"><?= $tidak_hadir; ?> Staff</span>
        </div>
    </div>

    <div
        class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group hover:border-amber-500 transition-all duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Masih Bekerja</p>
                <h3 class="text-2xl font-black text-slate-800"><?= $belum_pulang; ?></h3>
            </div>
            <div
                class="bg-slate-50 p-3 rounded-xl text-slate-400 group-hover:bg-amber-50 group-hover:text-amber-600 transition-colors">
                <i class="fas fa-hourglass-half text-lg"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-amber-500 animate-pulse">
            ● Menunggu Absen Pulang
        </div>
    </div>

    <div
        class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 group hover:border-indigo-500 transition-all duration-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Sedang Izin</p>
                <h3 class="text-2xl font-black text-slate-800"><?= $sedang_izin; ?></h3>
            </div>
            <div
                class="bg-slate-50 p-3 rounded-xl text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                <i class="fas fa-door-open text-lg"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-[10px] font-bold text-indigo-500">
            <i class="fas fa-info-circle mr-1"></i> Izin Keluar Jam Kerja
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 tracking-tight">Grafik Kehadiran (7 Hari)</h3>
            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">Tren stabilitas kehadiran
                karyawan</p>
        </div>
        <div class="h-64">
            <canvas id="grafikKehadiran"></canvas>
        </div>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
        <div class="mb-6">
            <h3 class="text-sm font-bold text-slate-800 tracking-tight">Aktivitas Terkini</h3>
            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">Log absensi masuk & pulang</p>
        </div>
        <div class="space-y-4">
            <?php
            $query_terkini = "SELECT a.*, k.nama FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id ORDER BY a.tanggal DESC, a.jam_masuk DESC LIMIT 5";
            $result_terkini = mysqli_query($koneksi, $query_terkini);
            while ($row = mysqli_fetch_assoc($result_terkini)) :
            ?>
                <div
                    class="flex items-center gap-4 p-3 rounded-xl border border-slate-50 hover:bg-slate-50 transition-colors">
                    <div
                        class="bg-indigo-50 text-indigo-500 w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs">
                        <?= substr($row['nama'], 0, 1); ?>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-700"><?= htmlspecialchars($row['nama']); ?></p>
                        <p class="text-[10px] text-slate-400"><?= date('H:i', strtotime($row['jam_masuk'])); ?> •
                            <?= $row['status'] == 'keluar' ? 'Selesai' : 'Mulai Kerja'; ?></p>
                    </div>
                    <?php if ($row['status'] == 'masuk'): ?>
                        <span
                            class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-amber-100 uppercase">Working</span>
                    <?php else: ?>
                        <span
                            class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-lg border border-emerald-100 uppercase">Home</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikKehadiran');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels); ?>,
            datasets: [{
                label: 'Kehadiran',
                data: <?= json_encode($data_kehadiran); ?>,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#4f46e5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

<?php include 'footer.php'; ?>