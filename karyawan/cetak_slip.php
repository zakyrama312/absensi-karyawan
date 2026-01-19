<?php
session_start();
require '../config.php';

// Proteksi Login
if (!isset($_SESSION['id_karyawan']) && !isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil data dari URL
$id_k = sanitize($koneksi, $_GET['id_karyawan']);
$bulan = sanitize($koneksi, $_GET['bulan']);
$tahun = sanitize($koneksi, $_GET['tahun']);

// Query data gaji dan join dengan data karyawan
$sql = "SELECT p.*, k.nama, k.nik, k.jabatan, k.bagian 
        FROM penggajian p 
        JOIN karyawan k ON p.id_karyawan = k.id 
        WHERE p.id_karyawan = '$id_k' AND p.bulan = '$bulan' AND p.tahun = '$tahun'";
$res = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($res);

if (!$data) {
    die("Data gaji tidak ditemukan untuk periode ini.");
}

// Konversi Nama Bulan ke Indonesia
$bulan_indo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
$nama_bulan = $bulan_indo[(int)$bulan];

// Tarif untuk keterangan
$tarif_lembur = 21590;
$tarif_mangkir = 83000;
$tarif_izin_keluar = 11857;
$tarif_insentif_pres = 2667;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?= $data['nama']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
    @media print {
        .no-print {
            display: none;
        }

        body {
            background: white;
            padding: 0;
        }

        .max-w-2xl {
            max-width: 100%;
            border: none;
            box-shadow: none;
        }
    }

    .font-mono-slip {
        font-family: 'Courier New', Courier, monospace;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 py-10">

    <div class="max-w-2xl mx-auto bg-white p-8 shadow-sm border border-gray-200 font-mono-slip text-sm">

        <div class="flex justify-between border-b-2 border-black pb-2 mb-4">
            <div>
                <h1 class="font-bold text-lg">PT. TRI LESTARI SANDANG INDUSTRY</h1>
                <p class="text-xs">TEGAL - JAWA TENGAH</p>
            </div>
            <div class="text-right">
                <h2 class="font-bold text-sm">Slip Gaji Bulanan Biasa</h2>
                <p>Bulan: <?= $nama_bulan . ' ' . $tahun; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 mb-4 text-[13px]">
            <div>Name: <span class="font-bold"><?= strtoupper($data['nama']); ?></span></div>
            <div>Nik: <?= $data['nik']; ?></div>
            <div class="text-right">Bagian: <?= $data['bagian'] ?? '-'; ?></div>
        </div>

        <div class="border-t border-dashed border-black pt-2">
            <div class="flex justify-between">
                <span>Gaji Pokok:</span>
                <span>RP<?= number_format($data['total_gaji_pokok'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between">
                <span>Tunjangan Tetap:</span>
                <span>RP<?= number_format($data['tunjangan_tetap'] ?? 0, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between font-bold border-t border-black mt-1">
                <span>Total Gaji Dasar</span>
                <span>RP<?= number_format($data['total_gaji_pokok'] + ($data['tunjangan_tetap'] ?? 0), 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="mt-4 space-y-1">
            <div class="flex justify-between text-xs italic text-gray-500">
                <span>Lembur & Tunjangan Kehadiran:</span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3 text-[12px]">Lembur Jam</span>
                <span class="text-gray-400 text-[10px]"><?= number_format($tarif_lembur, 0, ',', '.'); ?> X
                    <?= $data['jml_lembur_1'] ?? 0; ?> Jam</span>
                <span><?= number_format($data['lembur'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4 text-[12px]">
                <span class="w-1/3">Subsidi Transport</span>
                <span class="text-gray-400 text-[10px]">2.000 X <?= $data['jml_transport'] ?? 0; ?> Hari</span>
                <span><?= number_format($data['subsidi_transport'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4 text-[12px]">
                <span class="w-1/3">Subsidi Makan</span>
                <span class="text-gray-400 text-[10px]">3.000 X <?= $data['jml_makan'] ?? 0; ?> Hari</span>
                <span><?= number_format($data['subsidi_makan'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between font-bold border-t border-dashed border-black mt-1">
                <span>Total Lembur+Allow+Lain</span>
                <span><?= number_format($data['lembur'] + $data['subsidi_transport'] + $data['subsidi_makan'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="mt-4 border-t border-black pt-2 space-y-1">
            <div class="flex justify-between text-xs italic text-gray-500">
                <span>Daftar Potongan:</span>
            </div>
            <div class="flex justify-between ml-4 text-[12px]">
                <span class="w-1/3">Potongan Mangkir/Sakit</span>
                <span class="text-gray-400 text-[10px]"><?= number_format($tarif_mangkir, 0, ',', '.'); ?> X
                    <?= $data['jml_mangkir'] ?? 0; ?> Hari</span>
                <span><?= number_format($data['potongan_mangkir_sakit'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4 text-[12px]">
                <span class="w-1/3">Potongan Izin Keluar</span>
                <span class="text-gray-400 text-[10px]"><?= number_format($tarif_izin_keluar, 0, ',', '.'); ?> X
                    <?= $data['jml_izin_keluar'] ?? 0; ?> Jam</span>
                <span><?= number_format(($data['jml_izin_keluar'] ?? 0) * $tarif_izin_keluar, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4 text-[12px]">
                <span class="w-1/3">Potongan Insentif</span>
                <span class="text-gray-400 text-[10px]"><?= number_format($tarif_insentif_pres, 0, ',', '.'); ?> X
                    <?= $data['jml_mangkir'] ?? 0; ?> Hari</span>
                <span><?= number_format($data['insentif'] ?? 0, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4 text-[12px]">
                <span class="w-1/3">Potongan Prestasi</span>
                <span class="text-gray-400 text-[10px]"><?= number_format($tarif_insentif_pres, 0, ',', '.'); ?> X
                    <?= $data['jml_mangkir'] ?? 0; ?> Hari</span>
                <span><?= number_format($data['prestasi'] ?? 0, 0, ',', '.'); ?></span>
            </div>

            <?php
            $total_semua_potongan = $data['potongan_mangkir_sakit'] +
                (($data['jml_izin_keluar'] ?? 0) * $tarif_izin_keluar) +
                ($data['insentif'] ?? 0) + ($data['prestasi'] ?? 0);
            ?>
            <div class="flex justify-between font-bold border-t border-black mt-1">
                <span>Total Potongan</span>
                <span><?= number_format($total_semua_potongan, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="mt-6 flex justify-between text-lg font-bold border-b-4 border-double border-black pb-1">
            <span>Total Terima</span>
            <span>RP<?= number_format($data['total_terima'], 0, ',', '.'); ?></span>
        </div>

        <div class="mt-8 flex justify-between px-10">
            <div class="text-center">
                <p>Tegal, <?= date('d M Y', strtotime($data['tanggal_generate'])); ?></p>
                <div class="h-16"></div>
                <p class="border-t border-black font-bold"><?= strtoupper($data['nama']); ?></p>
            </div>
            <div class="text-center pt-8">
                <button onclick="window.print()"
                    class="no-print bg-indigo-600 text-white px-6 py-2 rounded-xl shadow-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-print mr-2"></i> Cetak Slip
                </button>
            </div>
        </div>
    </div>

    <div class="text-center mt-6 no-print">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'karyawan'): ?>
        <a href="riwayat_gaji_karyawan.php" class="text-indigo-600 hover:underline font-bold">← Kembali ke Riwayat
            Gaji</a>
        <?php else: ?>
        <a href="riwayat_gaji.php" class="text-indigo-600 hover:underline font-bold">← Kembali ke Data Gaji</a>
        <?php endif; ?>
    </div>

</body>

</html>