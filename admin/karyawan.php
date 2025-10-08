<?php
$page_title = 'Data Karyawan';
include 'header.php';

$pesan = '';
$error = '';

// Proses Tambah / Edit Karyawan
if (isset($_POST['simpan'])) {
    $id = isset($_POST['id']) ? sanitize($koneksi, $_POST['id']) : null;
    $username = sanitize($koneksi, $_POST['username']);
    $nama = sanitize($koneksi, $_POST['nama']);
    $jabatan = sanitize($koneksi, $_POST['jabatan']);
    $password = sanitize($koneksi, $_POST['password']);

    if (empty($username) || empty($nama)) {
        $error = "username dan Nama tidak boleh kosong.";
    } else {
        if ($id) { // Edit
            $query_update = "UPDATE karyawan SET username='$username', nama='$nama', jabatan='$jabatan'";
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query_update .= ", password='$hashed_password'";
            }
            $query_update .= " WHERE id=$id";
            if (mysqli_query($koneksi, $query_update)) {
                $pesan = "Data karyawan berhasil diperbarui.";
            } else {
                $error = "Gagal memperbarui data. username mungkin sudah ada.";
            }
        } else { // Tambah
            if (empty($password)) {
                $error = "Password tidak boleh kosong untuk karyawan baru.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query_insert = "INSERT INTO karyawan (username, nama, jabatan, password) VALUES ('$username', '$nama', '$jabatan', '$hashed_password')";
                if (mysqli_query($koneksi, $query_insert)) {
                    $pesan = "Karyawan baru berhasil ditambahkan.";
                } else {
                    $error = "Gagal menambah data. username mungkin sudah ada.";
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


<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-xl font-bold mb-4"><?php echo $edit_data ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru'; ?></h2>
    <form method="POST" action="karyawan.php" class="space-y-4">
        <input type="hidden" name="id" value="<?php echo $edit_data['id'] ?? ''; ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" value="<?php echo $edit_data['username'] ?? ''; ?>"
                    required
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="<?php echo $edit_data['nama'] ?? ''; ?>" required
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
                <input type="text" name="jabatan" id="jabatan" value="<?php echo $edit_data['jabatan'] ?? ''; ?>"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password"
                    class="mt-1 p-2 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="<?php echo $edit_data ? 'Kosongkan jika tidak diubah' : ''; ?>">
            </div>
        </div>
        <div class="flex justify-end">
            <?php if ($edit_data): ?>
                <a href="karyawan.php" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 mr-2">Batal
                    Edit</a>
            <?php endif; ?>
            <button type="submit" name="simpan"
                class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Simpan Data</button>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">Daftar Karyawan</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500" id="karyawanTable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-6">Username</th>
                    <th class="py-3 px-6">Nama</th>
                    <th class="py-3 px-6">Jabatan</th>
                    <th class="py-3 px-6">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="bg-white border-b">
                        <td class="py-4 px-6"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-4 px-6 font-medium text-gray-900"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($row['jabatan']); ?></td>
                        <td class="py-4 px-6 flex space-x-2">
                            <a href="karyawan.php?edit=<?php echo $row['id']; ?>"
                                class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></a>
                            <a href="karyawan.php?hapus=<?php echo $row['id']; ?>"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>