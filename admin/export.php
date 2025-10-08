<?php
require '../config.php';

// Cek sesi login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Set header untuk download file Excel
$nama_file = "Laporan_Absensi_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$nama_file\"");

// Ambil filter dari URL
$filter_tanggal = isset($_GET['tanggal']) ? sanitize($koneksi, $_GET['tanggal']) : '';
$filter_karyawan = isset($_GET['karyawan']) ? sanitize($koneksi, $_GET['karyawan']) : '';

$where_clause = "WHERE 1=1";
if (!empty($filter_tanggal)) {
    $where_clause .= " AND a.tanggal = '$filter_tanggal'";
}
if (!empty($filter_karyawan)) {
    $where_clause .= " AND k.id = '$filter_karyawan'";
}

// Query data
$query = "SELECT a.*, k.nama, k.username, k.jabatan FROM absensi a JOIN karyawan k ON a.id_karyawan = k.id $where_clause ORDER BY a.tanggal DESC, k.nama ASC";
$result = mysqli_query($koneksi, $query);

// Buat tabel HTML untuk output
$output = '<table border="1">
    <thead>
        <tr>
            <th>username</th>
            <th>Nama Karyawan</th>
            <th>Jabatan</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Total Jam Kerja (Jam)</th>
        </tr>
    </thead>
    <tbody>';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $total_jam_kerja_desimal = '';
        if ($row['jam_masuk'] && $row['jam_keluar']) {
            $jam_masuk = new DateTime($row['jam_masuk']);
            $jam_keluar = new DateTime($row['jam_keluar']);
            $selisih_detik = $jam_keluar->getTimestamp() - $jam_masuk->getTimestamp();
            $total_jam_kerja_desimal = round($selisih_detik / 3600, 2); // Konversi ke jam desimal
        }

        $output .= '
        <tr>
            <td>' . htmlspecialchars($row['username']) . '</td>
            <td>' . htmlspecialchars($row['nama']) . '</td>
            <td>' . htmlspecialchars($row['jabatan']) . '</td>
            <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
            <td>' . $row['jam_masuk'] . '</td>
            <td>' . ($row['jam_keluar'] ?? '') . '</td>
            <td>' . $total_jam_kerja_desimal . '</td>
        </tr>';
    }
} else {
    $output .= '<tr><td colspan="7">Tidak ada data.</td></tr>';
}

$output .= '</tbody></table>';

// Tampilkan output
echo $output;

exit();
