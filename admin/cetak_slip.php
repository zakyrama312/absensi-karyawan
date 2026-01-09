<?php
require '../config.php';

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

// Konversi Nama Bulan
$nama_bulan = date('F', mktime(0, 0, 0, $bulan, 10));

// Tarif untuk keterangan (Sesuai image_3d5724.png)
$tarif_lembur = 21590;
$tarif_mangkir = 83000;
$tarif_izin_keluar = 11857;
$tarif_insentif_pres = 2667;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?php echo $data['nama']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                shadow: none;
            }
        }

        .font-mono-slip {
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>

<body class="bg-gray-200 py-10">

    <div class="max-w-2xl mx-auto bg-white p-8 shadow-lg border border-gray-300 font-mono-slip text-sm">

        <div class="flex justify-between border-b-2 border-black pb-2 mb-4">
            <div>
                <h1 class="font-bold text-lg">PT. TRI LESTARI SANDANG INDUSTRY</h1>
                <p>TEGAL - JAWA TENGAH</p>
            </div>
            <div class="text-right">
                <h2 class="font-bold text-sm">Slip Gaji Bulanan Biasa</h2>
                <p>Bulan: <?php echo $nama_bulan . ' ' . $tahun; ?></p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 mb-4">
            <div>Name: <span class="font-bold"><?php echo strtoupper($data['nama']); ?></span></div>
            <div>Nik: <?php echo $data['nik']; ?></div>
            <div class="text-right">Bagian: <?php echo $data['bagian'] ?? '-'; ?></div>
        </div>

        <div class="border-t border-dashed border-black pt-2">
            <div class="flex justify-between">
                <span>Gaji Pokok:</span>
                <span>RP<?php echo number_format($data['total_gaji_pokok'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between">
                <span>Tunjangan Tetap:</span>
                <span>RP<?php echo number_format($data['tunjangan_tetap'] ?? 0, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between font-bold border-t border-black mt-1">
                <span>Total Gaji Dasar</span>
                <span>RP<?php echo number_format($data['total_gaji_pokok'] + ($data['tunjangan_tetap'] ?? 0), 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="mt-4 space-y-1">
            <div class="flex justify-between text-xs italic text-gray-600">
                <span>Lembur & Tunjangan Kehadiran:</span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Lembur Jam</span>
                <span class="text-gray-500 text-xs"><?php echo number_format($tarif_lembur, 0, ',', '.'); ?> X
                    <?php echo $data['jml_lembur_1'] ?? 0; ?> Jam</span>
                <span><?php echo number_format($data['lembur'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Subsidi Transport</span>
                <span class="text-gray-500 text-xs">2.000 X <?php echo $data['jml_transport'] ?? 0; ?> Hari</span>
                <span><?php echo number_format($data['subsidi_transport'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Subsidi Makan</span>
                <span class="text-gray-500 text-xs">3.000 X <?php echo $data['jml_makan'] ?? 0; ?> Hari</span>
                <span><?php echo number_format($data['subsidi_makan'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between font-bold border-t border-dashed border-black mt-1">
                <span>Total Lembur+Allow+Lain</span>
                <span><?php echo number_format($data['lembur'] + $data['subsidi_transport'] + $data['subsidi_makan'], 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="mt-4 border-t border-black pt-2 space-y-1">
            <div class="flex justify-between text-xs italic text-gray-600">
                <span>Daftar Potongan:</span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Potongan Mangkir/Sakit</span>
                <span class="text-gray-500 text-xs"><?php echo number_format($tarif_mangkir, 0, ',', '.'); ?> X
                    <?php echo $data['jml_mangkir'] ?? 0; ?> Hari</span>
                <span><?php echo number_format($data['potongan_mangkir_sakit'], 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Potongan Izin Keluar</span>
                <span class="text-gray-500 text-xs"><?php echo number_format($tarif_izin_keluar, 0, ',', '.'); ?> X
                    <?php echo $data['jml_izin_keluar'] ?? 0; ?> Jam</span>
                <span><?php echo number_format(($data['jml_izin_keluar'] ?? 0) * $tarif_izin_keluar, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Potongan Insentif</span>
                <span class="text-gray-500 text-xs"><?php echo number_format($tarif_insentif_pres, 0, ',', '.'); ?> X
                    <?php echo $data['jml_mangkir'] ?? 0; ?> Hari</span>
                <span><?php echo number_format($data['insentif'] ?? 0, 0, ',', '.'); ?></span>
            </div>
            <div class="flex justify-between ml-4">
                <span class="w-1/3">Potongan Prestasi</span>
                <span class="text-gray-500 text-xs"><?php echo number_format($tarif_insentif_pres, 0, ',', '.'); ?> X
                    <?php echo $data['jml_mangkir'] ?? 0; ?> Hari</span>
                <span><?php echo number_format($data['prestasi'] ?? 0, 0, ',', '.'); ?></span>
            </div>

            <?php
            // Hitung total semua potongan
            $total_semua_potongan = $data['potongan_mangkir_sakit'] +
                (($data['jml_izin_keluar'] ?? 0) * $tarif_izin_keluar) +
                ($data['insentif'] ?? 0) +
                ($data['prestasi'] ?? 0) +
                $data['potongan_bpjs'];
            ?>
            <div class="flex justify-between font-bold border-t border-black mt-1">
                <span>Total Potongan</span>
                <span><?php echo number_format($total_semua_potongan, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="mt-6 flex justify-between text-lg font-bold border-b-2 border-double border-black pb-1">
            <span>Total Terima</span>
            <span>RP<?php echo number_format($data['total_terima'], 0, ',', '.'); ?></span>
        </div>

        <div class="mt-8 flex justify-between px-10">
            <div class="text-center">
                <p>Tegal, <?php echo date('d M Y'); ?></p>
                <div class="h-16"></div>
                <p class="border-t border-black font-bold"><?php echo strtoupper($data['nama']); ?></p>
            </div>
            <div class="text-center pt-5">
                <button onclick="window.print()"
                    class="no-print bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                    Cetak Slip
                </button>
            </div>
        </div>

    </div>

    <div class="text-center mt-6 no-print">
        <a href="generate_gaji.php" class="text-indigo-600 hover:underline">← Kembali ke Data Gaji</a>
    </div>

</body>

</html>