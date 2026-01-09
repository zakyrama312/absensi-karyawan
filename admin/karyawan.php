<?php
$page_title = 'Data Karyawan';
include 'header.php';

$pesan = '';
$error = '';

// Proses Tambah / Edit Karyawan
if (isset($_POST['simpan'])) {
    $id = isset($_POST['id']) ? sanitize($koneksi, $_POST['id']) : null;
    $nik = sanitize($koneksi, $_POST['nik']); // Tambahan NIK
    $username = sanitize($koneksi, $_POST['username']);
    $nama = sanitize($koneksi, $_POST['nama']);
    $jabatan = sanitize($koneksi, $_POST['jabatan']);
    $bagian = sanitize($koneksi, $_POST['bagian']); // Tambahan Bagian
    $gaji_pokok = sanitize($koneksi, $_POST['gaji_pokok']); // Tambahan Gaji
    $tunjangan_tetap = sanitize($koneksi, $_POST['tunjangan_tetap']); // Tambahan Tunjangan
    $password = sanitize($koneksi, $_POST['password']);

    if (empty($nik) || empty($username) || empty($nama)) {
        $error = "NIK, Username, dan Nama tidak boleh kosong.";
    } else {
        if ($id) { // Edit
            $query_update = "UPDATE karyawan SET 
                             nik='$nik', 
                             username='$username', 
                             nama='$nama', 
                             jabatan='$jabatan', 
                             bagian='$bagian', 
                             gaji_pokok='$gaji_pokok', 
                             tunjangan_tetap='$tunjangan_tetap'";

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query_update .= ", password='$hashed_password'";
            }
            $query_update .= " WHERE id=$id";

            if (mysqli_query($koneksi, $query_update)) {
                $pesan = "Data karyawan berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui data. NIK atau Username mungkin sudah ada.";
            }
        } else { // Tambah
            if (empty($password)) {
                $error = "Password tidak boleh kosong untuk karyawan baru.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query_insert = "INSERT INTO karyawan (nik, username, nama, jabatan, bagian, gaji_pokok, tunjangan_tetap, password) 
                                 VALUES ('$nik', '$username', '$nama', '$jabatan', '$bagian', '$gaji_pokok', '$tunjangan_tetap', '$hashed_password')";

                if (mysqli_query($koneksi, $query_insert)) {
                    $pesan = "Karyawan baru berhasil ditambahkan.";
                } else {
                    $error = "Gagal menambah data. NIK atau Username mungkin sudah digunakan.";
                }
            }
        }
    }
}

// Proses Hapus Karyawan
if (isset($_GET['hapus'])) {
    $id = sanitize($koneksi, $_GET['hapus']);
    $query_delete = "DELETE FROM karyawan WHERE id=$id";
    if (mysqli_query($koneksi, $query_delete)) {
        $pesan = "Data karyawan berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data.";
    }
}

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = sanitize($koneksi, $_GET['edit']);
    $query_edit = "SELECT * FROM karyawan WHERE id=$id";
    $result_edit = mysqli_query($koneksi, $query_edit);
    $edit_data = mysqli_fetch_assoc($result_edit);
}

$query = "SELECT * FROM karyawan ORDER BY nama ASC";
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

<div class="bg-white p-6 rounded-lg shadow-md mb-6 border-t-4 border-indigo-600">
    <div class="flex items-center gap-3 mb-8">
        <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
            <i class="fas <?php echo $edit_data ? 'fa-user-edit' : 'fa-user-plus'; ?>"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">
                <?php echo $edit_data ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru'; ?>
            </h2>
            <p class="text-xs text-slate-400 font-medium">
                <?php echo $edit_data ? 'Perbarui informasi profil dan detail kepegawaian karyawan.' : 'Masukkan data lengkap untuk mendaftarkan karyawan baru ke dalam sistem.'; ?>
            </p>
        </div>
    </div>
    <form method="POST" action="karyawan.php" class="space-y-4">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? ''; ?>">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">NIK (Nomor Induk)</label>
                <input type="text" name="nik" value="<?php echo $edit_data['nik'] ?? ''; ?>" required
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" value="<?php echo $edit_data['username'] ?? ''; ?>" required
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="<?php echo $edit_data ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter'; ?>">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" value="<?php echo $edit_data['nama'] ?? ''; ?>" required
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Jabatan</label>
                <input type="text" name="jabatan" value="<?php echo $edit_data['jabatan'] ?? ''; ?>"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Bagian</label>
                <input type="text" name="bagian" value="<?php echo $edit_data['bagian'] ?? ''; ?>"
                    placeholder="Contoh: Q.C LAMPU"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="bg-gray-50 p-3 rounded-md border border-gray-200">
                <label class="block text-sm font-bold text-indigo-700">Gaji Pokok (Rp)</label>
                <input type="number" name="gaji_pokok" value="<?php echo $edit_data['gaji_pokok'] ?? '0'; ?>"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="bg-gray-50 p-3 rounded-md border border-gray-200">
                <label class="block text-sm font-bold text-indigo-700">Tunjangan Tetap (Rp)</label>
                <input type="number" name="tunjangan_tetap" value="<?php echo $edit_data['tunjangan_tetap'] ?? '0'; ?>"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <?php if ($edit_data): ?>
                <a href="karyawan.php" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 mr-2">Batal</a>
            <?php endif; ?>
            <button type="submit" name="simpan"
                class="bg-indigo-600 text-white py-2 px-6 rounded-md hover:bg-indigo-700 shadow-lg transition">Simpan
                Data Karyawan</button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex items-center gap-3 mb-8">
        <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
            <i class="fas fa-users-viewfinder"></i>
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">Daftar Karyawan</h2>
            <p class="text-xs text-slate-400 font-medium">Kelola data profil, jabatan, dan status keaktifan seluruh
                karyawan sistem.</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500" id="daftarKaryawanTable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="py-3 px-6 text-center">NIK</th>
                    <th class="py-3 px-6">Nama / Bagian</th>
                    <th class="py-3 px-6">Jabatan</th>
                    <th class="py-3 px-6">Gaji Pokok</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                        <td class="py-4 px-6 font-mono text-xs text-center"><?php echo htmlspecialchars($row['nik']); ?>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></div>
                            <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['bagian'] ?? '-'); ?></div>
                        </td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($row['jabatan']); ?></td>
                        <td class="py-4 px-6 font-semibold text-green-600">
                            Rp <?php echo number_format($row['gaji_pokok'], 0, ',', '.'); ?>
                        </td>
                        <td class="py-4 px-6 flex justify-center space-x-3">
                            <a href="karyawan.php?edit=<?php echo $row['id']; ?>"
                                class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></a>
                            <a href="karyawan.php?hapus=<?php echo $row['id']; ?>"
                                onclick="return confirm('Hapus karyawan ini? Seluruh data absen & jadwal juga akan terhapus!')"
                                class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>