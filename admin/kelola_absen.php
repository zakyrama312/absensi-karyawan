<?php
$page_title = 'Kelola Data Absensi';
include 'header.php';

$pesan = '';
$error = '';

// Proses Simpan Perubahan
if (isset($_POST['simpan'])) {
    $id = sanitize($koneksi, $_POST['id']);
    $jam_masuk = sanitize($koneksi, $_POST['jam_masuk']);
    $jam_keluar = sanitize($koneksi, $_POST['jam_keluar']);

    // Validasi format waktu
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

// Proses Hapus Absen
if (isset($_GET['hapus'])) {
    $id = sanitize($koneksi, $_GET['hapus']);
    $query_delete = "DELETE FROM absensi WHERE id=$id";
    if (mysqli_query($koneksi, $query_delete)) {
        $pesan = "Data absensi berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data.";
    }
}

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = sanitize($koneksi, $_GET['edit']);
    $query_edit = "SELECT a.*, k.nama FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id WHERE a.id=$id";
    $result_edit = mysqli_query($koneksi, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

// Sama dengan filter di riwayat.php
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

<?php if ($pesan): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p><?php echo $pesan; ?></p>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p><?php echo $error; ?></p>
    </div>
<?php endif; ?>

<?php if ($edit_data): ?>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-bold mb-4">Edit Absensi: <?php echo htmlspecialchars($edit_data['nama']); ?>
            (<?php echo date('d M Y', strtotime($edit_data['tanggal'])); ?>)</h2>
        <form method="POST" action="kelola_absen.php">
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="jam_masuk" class="block text-sm font-medium text-gray-700">Jam Masuk</label>
                    <input type="text" name="jam_masuk" id="jam_masuk" value="<?php echo $edit_data['jam_masuk'] ?? ''; ?>"
                        class="mt-1 border p-2 block w-full rounded-md border-gray-300 shadow-sm" placeholder="HH:MM:SS">
                </div>
                <div>
                    <label for="jam_keluar" class="block text-sm font-medium text-gray-700">Jam Keluar</label>
                    <input type="text" name="jam_keluar" id="jam_keluar"
                        value="<?php echo $edit_data['jam_keluar'] ?? ''; ?>"
                        class="mt-1 border p-2 block w-full rounded-md border-gray-300 shadow-sm" placeholder="HH:MM:SS">
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <a href="kelola_absen.php"
                    class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 mr-2">Batal</a>
                <button type="submit" name="simpan"
                    class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label for="tanggal" class="block text-sm font-medium text-gray-700">Filter Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="<?php echo $filter_tanggal; ?>"
                class="mt-1 block w-full border p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label for="karyawan" class="block text-sm font-medium text-gray-700">Filter Karyawan</label>
            <select name="karyawan" id="karyawan"
                class="mt-1 block border p-2 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
        <div class="flex items-end md:self-end m-1">
            <button type="submit"
                class="w-full md:w-auto bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Filter</button>
            <a href="kelola_absen.php"
                class="ml-2 w-full md:w-auto bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 text-center">Reset</a>
        </div>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-6">Nama Karyawan</th>
                    <th class="py-3 px-6">Tanggal</th>
                    <th class="py-3 px-6">Jam Masuk</th>
                    <th class="py-3 px-6">Jam Keluar</th>
                    <th class="py-3 px-6">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-4 px-6"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td class="py-4 px-6"><?php echo $row['jam_masuk'] ?? '-'; ?></td>
                        <td class="py-4 px-6"><?php echo $row['jam_keluar'] ?? '-'; ?></td>
                        <td class="py-4 px-6 flex space-x-2">
                            <a href="kelola_absen.php?edit=<?php echo $row['id']; ?>"
                                class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></a>
                            <a href="kelola_absen.php?hapus=<?php echo $row['id']; ?>"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
                <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr class="bg-white border-b">
                        <td colspan="5" class="py-4 px-6 text-center text-gray-500">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>