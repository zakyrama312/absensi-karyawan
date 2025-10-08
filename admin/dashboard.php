<?php
$page_title = 'Dashboard';
include 'header.php';

// --- Data untuk Statistik Box ---
// Jumlah Karyawan
$query_total_karyawan = "SELECT COUNT(id) as total FROM karyawan";
$result_total_karyawan = mysqli_query($koneksi, $query_total_karyawan);
$total_karyawan = mysqli_fetch_assoc($result_total_karyawan)['total'];

// Karyawan Hadir Hari Ini
$tanggal_hari_ini = date('Y-m-d');
$query_hadir_hari_ini = "SELECT COUNT(DISTINCT id_karyawan) as total FROM absensi WHERE tanggal = '$tanggal_hari_ini'";
$result_hadir_hari_ini = mysqli_query($koneksi, $query_hadir_hari_ini);
$hadir_hari_ini = mysqli_fetch_assoc($result_hadir_hari_ini)['total'];

// Karyawan Belum Absen Pulang
$query_belum_pulang = "SELECT COUNT(id) as total FROM absensi WHERE tanggal = '$tanggal_hari_ini' AND jam_keluar IS NULL";
$result_belum_pulang = mysqli_query($koneksi, $query_belum_pulang);
$belum_pulang = mysqli_fetch_assoc($result_belum_pulang)['total'];

$tidak_hadir = $total_karyawan - $hadir_hari_ini;

// --- Data untuk Grafik ---
$labels = [];
$data_kehadiran = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($tanggal));
    
    $query_grafik = "SELECT COUNT(DISTINCT id_karyawan) as total FROM absensi WHERE tanggal = '$tanggal'";
    $result_grafik = mysqli_query($koneksi, $query_grafik);
    $data_kehadiran[] = mysqli_fetch_assoc($result_grafik)['total'];
}
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="bg-blue-500 text-white p-4 rounded-full">
            <i class="fas fa-users fa-2x"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-600">Total Karyawan</h3>
            <p class="text-2xl font-bold"><?php echo $total_karyawan; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="bg-green-500 text-white p-4 rounded-full">
            <i class="fas fa-user-check fa-2x"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-600">Hadir Hari Ini</h3>
            <p class="text-2xl font-bold"><?php echo $hadir_hari_ini; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="bg-yellow-500 text-white p-4 rounded-full">
            <i class="fas fa-hourglass-half fa-2x"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-600">Belum Absen Pulang</h3>
            <p class="text-2xl font-bold"><?php echo $belum_pulang; ?></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
        <div class="bg-red-500 text-white p-4 rounded-full">
            <i class="fas fa-user-times fa-2x"></i>
        </div>
        <div class="ml-4">
            <h3 class="text-lg font-semibold text-gray-600">Tidak Hadir</h3>
            <p class="text-2xl font-bold"><?php echo $tidak_hadir; ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Grafik Kehadiran 7 Hari Terakhir</h2>
        <div class="chart-container" style="position: relative; height:40vh; width:100%">
            <canvas id="grafikKehadiran"></canvas>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Aktivitas Absensi Terkini</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 px-6">Nama Karyawan</th>
                        <th scope="col" class="py-3 px-6">Waktu</th>
                        <th scope="col" class="py-3 px-6">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_terkini = "SELECT a.*, k.nama FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id ORDER BY a.tanggal DESC, a.jam_masuk DESC, a.jam_keluar DESC LIMIT 5";
                    $result_terkini = mysqli_query($koneksi, $query_terkini);
                    while ($row = mysqli_fetch_assoc($result_terkini)) {
                    ?>
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama']); ?>
                        </td>
                        <td class="py-4 px-6"><?php echo date('d M Y', strtotime($row['tanggal'])); ?> -
                            <?php echo $row['status'] == 'masuk' ? $row['jam_masuk'] : $row['jam_keluar']; ?></td>
                        <td class="py-4 px-6">
                            <?php 
                                    if($row['status'] == 'masuk' && $row['jam_keluar'] == null) echo '<span class="bg-yellow-200 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Masuk</span>';
                                    else if($row['status'] == 'keluar') echo '<span class="bg-green-200 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Pulang</span>';
                                ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('grafikKehadiran');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Jumlah Karyawan Hadir',
            data: <?php echo json_encode($data_kehadiran); ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include 'footer.php'; ?>