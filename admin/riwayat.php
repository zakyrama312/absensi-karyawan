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

$query = "SELECT a.*, k.nama, k.username FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id $where_clause ORDER BY a.tanggal DESC, a.jam_masuk DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 items-end">
        <div>
            <label for="tanggal" class="block text-sm font-medium text-gray-700">Filter Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="<?php echo $filter_tanggal; ?>"
                class="mt-1  border p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label for="karyawan" class="block text-sm font-medium text-gray-700">Filter Karyawan</label>
            <select name="karyawan" id="karyawan"
                class="mt-1  border p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Semua Karyawan</option>
                <?php
                $query_karyawan = "SELECT id, nama FROM karyawan ORDER BY nama ASC";
                $result_karyawan = mysqli_query($koneksi, $query_karyawan);
                while ($row_karyawan = mysqli_fetch_assoc($result_karyawan)) {
                    $selected = ($filter_karyawan == $row_karyawan['id']) ? 'selected' : '';
                    echo "<option value='{$row_karyawan['id']}' $selected>" . htmlspecialchars($row_karyawan['nama']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="flex space-x-2">
            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="riwayat.php"
                class="w-full bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 text-center">
                <i class="fas fa-sync-alt mr-2"></i>Reset
            </a>
        </div>
        <div>
            <a href="export.php?tanggal=<?php echo $filter_tanggal; ?>&karyawan=<?php echo $filter_karyawan; ?>"
                class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 flex items-center justify-center">
                <i class="fas fa-file-excel mr-2"></i>Ekspor ke Excel
            </a>
        </div>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500" id="riwayatTable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-6">Username</th>
                    <th class="py-3 px-6">Nama Karyawan</th>
                    <th class="py-3 px-6">Tanggal</th>
                    <th class="py-3 px-6">Jam Masuk</th>
                    <th class="py-3 px-6">Jam Keluar</th>
                    <th class="py-3 px-6">Total Jam Kerja</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) {
                    $total_jam_kerja = '-';
                    if ($row['jam_masuk'] && $row['jam_keluar']) {
                        $jam_masuk = new DateTime($row['jam_masuk']);
                        $jam_keluar = new DateTime($row['jam_keluar']);
                        $durasi = $jam_masuk->diff($jam_keluar);
                        $total_jam_kerja = $durasi->format('%h jam %i menit');
                    }
                ?>
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-4 px-6 font-medium text-gray-900">
                            <?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td class="py-4 px-6 font-medium text-gray-900"><?php echo $row['jam_masuk']; ?></td>
                        <td class="py-4 px-6 font-medium text-gray-900"><?php echo $row['jam_keluar'] ?? '-'; ?></td>
                        <td class="py-4 px-6 font-medium text-gray-900 font-bold"><?php echo $total_jam_kerja; ?></td>
                    </tr>
                <?php } ?>
                <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr class="bg-white border-b">
                        <td colspan="6" class="py-4 px-6 text-center text-gray-500">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>