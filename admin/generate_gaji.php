<?php
$page_title = 'Generate Gaji Karyawan';
include 'header.php';

// 1. Filter Periode
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// 2. Konfigurasi Tarif
$tarif_makan = 3000;
$tarif_transport = 2000;
$tarif_lembur_per_jam = 21590;
$potongan_mangkir = 83000;

// Tarif Potongan Baru Berdasarkan image_3d5724.png
$tarif_izin_keluar_per_jam = 11857;
$tarif_pot_insentif = 2667;
$tarif_pot_prestasi = 2667;

$query_karyawan = "SELECT * FROM karyawan ORDER BY nama ASC";
$result_karyawan = mysqli_query($koneksi, $query_karyawan);
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 shadow-sm">
                <i class="fas fa-file-invoice-dollar text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-800 tracking-tight">
                    Generate Gaji Periode: <span class="text-indigo-600"><?= $bulan . " / " . $tahun; ?></span>
                </h2>
                <p class="text-xs text-slate-400 font-medium">
                    Hitung otomatis gaji bersih berdasarkan absensi, lembur, dan potongan periode ini.
                </p>
            </div>
        </div>

        <div class="hidden md:block">
            <span
                class="bg-slate-100 text-slate-500 text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider border border-slate-200">
                Payroll Processing
            </span>
        </div>
    </div>

    <form method="GET" class="flex gap-4 mb-8 bg-gray-50 p-4 rounded-lg">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Bulan</label>
            <select name="bulan" class="border p-2 rounded-md w-40">
                <?php
                $nama_bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                for ($i = 1; $i <= 12; $i++) {
                    $selected = ($i == $filter_bulan) ? 'selected' : '';
                    echo "<option value='$i' $selected>$nama_bulan[$i]</option>";
                }
                ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tahun</label>
            <select name="tahun" class="border p-2 rounded-md w-32">
                <?php for ($y = 2024; $y <= 2026; $y++) : ?>
                    <option value="<?= $y; ?>" <?= $tahun == $y ? 'selected' : ''; ?>><?= $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <button type="submit"
            class="self-end bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse" id="gajiTable">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="py-3 px-4">Karyawan</th>
                    <th class="py-3 px-4 text-center">Kehadiran (M/T)</th>
                    <th class="py-3 px-4 text-center">Lembur</th>
                    <th class="py-3 px-4 text-center">Potongan (M/I/P/Iz)</th>
                    <th class="py-3 px-4 text-right">Gaji Bersih</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($k = mysqli_fetch_assoc($result_karyawan)) :
                    $id_k = $k['id'];

                    // 1. Hitung Kehadiran
                    $sql_hadir = "SELECT COUNT(*) as total FROM absensi WHERE id_karyawan='$id_k' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun' AND status='keluar'";
                    $total_hadir = mysqli_fetch_assoc(mysqli_query($koneksi, $sql_hadir))['total'];
                    $total_subsidi_makan = $total_hadir * $tarif_makan;
                    $total_subsidi_transport = $total_hadir * $tarif_transport;

                    // Hitung Lembur (Logika Aman Tengah Malam)
                    $total_jam_lembur = 0;
                    $rincian_lembur = [];

                    $sql_lembur = "SELECT a.tanggal, a.jam_keluar, s.jam_keluar as jam_seharusnya 
               FROM absensi a 
               JOIN jadwal_kerja jk ON a.id_jadwal = jk.id 
               JOIN shifts s ON jk.id_shift = s.id 
               WHERE a.id_karyawan='$id_k' AND MONTH(a.tanggal)='$bulan' AND YEAR(a.tanggal)='$tahun'";

                    $res_lembur = mysqli_query($koneksi, $sql_lembur);
                    while ($l = mysqli_fetch_assoc($res_lembur)) {
                        $out_asli = strtotime($l['jam_keluar']);
                        $out_jadwal = strtotime($l['jam_seharusnya']);

                        // Jika jam keluar asli lebih kecil dari jadwal (berarti lewat tengah malam)
                        // Contoh: Keluar 01:00, Jadwal 23:00
                        if ($out_asli < $out_jadwal) {
                            $diff = ($out_asli + 86400) - $out_jadwal; // Tambah 24 jam dalam detik
                        } else {
                            $diff = $out_asli - $out_jadwal;
                        }

                        $menit = floor($diff / 60);
                        if ($menit >= 30) {
                            $jam = floor($menit / 60);
                            $total_jam_lembur += $jam;
                            $tgl_f = date('d M Y', strtotime($l['tanggal']));
                            $rincian_lembur[] = "Tanggal $tgl_f : <b>$jam Jam</b> (Selesai: " . $l['jam_keluar'] . ")";
                        }
                    }
                    $detail_json = htmlspecialchars(json_encode($rincian_lembur));
                    $total_nominal_lembur = $total_jam_lembur * $tarif_lembur_per_jam;

                    // 3. Hitung Mangkir & Potongan Baru
                    // $sql_mangkir = "SELECT COUNT(*) as total FROM jadwal_kerja jk 
                    //                 LEFT JOIN absensi a ON jk.id_karyawan = a.id_karyawan AND jk.tanggal = a.tanggal
                    //                 WHERE jk.id_karyawan='$id_k' AND jk.id_shift != 3 
                    //                 AND DAYOFWEEK(jk.tanggal) NOT IN (1, 7) 
                    //                 AND MONTH(jk.tanggal)='$bulan' AND YEAR(jk.tanggal)='$tahun' AND a.id IS NULL";

                    $sql_mangkir = "SELECT COUNT(*) as total FROM jadwal_kerja jk 
                LEFT JOIN absensi a ON jk.id_karyawan = a.id_karyawan AND jk.tanggal = a.tanggal
                WHERE jk.id_karyawan='$id_k' 
                AND jk.id_shift != 3 
                AND DAYOFWEEK(jk.tanggal) != 1 -- HANYA MENGECUALIKAN MINGGU (1)
                AND MONTH(jk.tanggal)='$bulan' 
                AND YEAR(jk.tanggal)='$tahun' 
                AND a.id IS NULL";
                    $total_mangkir = mysqli_fetch_assoc(mysqli_query($koneksi, $sql_mangkir))['total'];


                    $pot_mangkir_nom = $total_mangkir * $potongan_mangkir;
                    $pot_insentif_nom = $total_mangkir * $tarif_pot_insentif;
                    $pot_prestasi_nom = $total_mangkir * $tarif_pot_prestasi;

                    // 4. HITUNG IZIN KELUAR OTOMATIS
                    $sql_izin = "SELECT SUM(HOUR(TIMEDIFF(jam_kembali, jam_pergi))) as total_jam 
                                 FROM izin_keluar 
                                 WHERE id_karyawan = '$id_k' 
                                 AND MONTH(tanggal) = '$bulan' 
                                 AND YEAR(tanggal) = '$tahun' 
                                 AND status_izin = 'kembali'";
                    $res_izin = mysqli_query($koneksi, $sql_izin);
                    $data_izin = mysqli_fetch_assoc($res_izin);
                    $jml_izin = $data_izin['total_jam'] ?? 0;
                    $pot_izin_nom = $jml_izin * $tarif_izin_keluar_per_jam;

                    // 5. Hitung Gaji Bersih
                    $gaji_pokok_tunjangan = $k['gaji_pokok'] + $k['tunjangan_tetap'];
                    $potongan_bpjs = 0; // Sesuai request client

                    $total_pendapatan = $gaji_pokok_tunjangan + $total_subsidi_makan + $total_subsidi_transport + $total_nominal_lembur;
                    $total_semua_potongan = $pot_mangkir_nom + $pot_insentif_nom + $pot_prestasi_nom + $pot_izin_nom + $potongan_bpjs;
                    $total_terima = $total_pendapatan - $total_semua_potongan;
                ?>
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="py-4 px-4">
                            <div class="font-bold text-gray-700"><?php echo $k['nama']; ?></div>
                            <div class="text-xs text-gray-400"><?php echo $k['jabatan']; ?></div>
                        </td>
                        <td class="">
                            <div class="font-medium text-green-600"><?php echo $total_hadir; ?> Hari</div>
                            <div class="text-[10px] text-gray-400">M:
                                <?= number_format($total_subsidi_makan, 0, ',', '.') ?> | T:
                                <?= number_format($total_subsidi_transport, 0, ',', '.') ?></div>
                        </td>
                        <td class="">
                            <button type="button" onclick="openModal('<?= $nama_k ?>', '<?= $detail_json ?>')"
                                class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-bold hover:bg-blue-100 transition border border-blue-200">
                                <?= $total_jam_lembur ?> Jam <i class="fas fa-info-circle ml-1"></i>
                            </button>
                            <div class="text-[10px] text-blue-500">Rp
                                <?= number_format($total_nominal_lembur, 0, ',', '.') ?></div>

                        </td>
                        <td class="">
                            <div class="font-bold text-red-500"><?php echo $total_mangkir; ?> Hari</div>
                            <div class="text-[9px] text-red-400">Ins: <?= number_format($pot_insentif_nom, 0, ',', '.') ?> |
                                Pres: <?= number_format($pot_prestasi_nom, 0, ',', '.') ?></div>
                            <div class="text-[9px] text-red-500 font-semibold">Izin: <?= $jml_izin; ?> Jam (Rp
                                <?= number_format($pot_izin_nom, 0, ',', '.') ?>)</div>
                        </td>
                        <td class="py-4 px-4 font-bold text-indigo-600">
                            Rp <?php echo number_format($total_terima, 0, ',', '.'); ?>
                        </td>
                        <td class="">
                            <a href="simpan_gaji.php?id=<?= $id_k ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&jml_makan=<?= $total_hadir ?>&jml_transport=<?= $total_hadir ?>&subsidi_makan=<?= $total_subsidi_makan ?>&subsidi_transport=<?= $total_subsidi_transport ?>&lembur=<?= $total_nominal_lembur ?>&potongan_mangkir=<?= $pot_mangkir_nom ?>&total_terima=<?= $total_terima ?>&jml_lembur=<?= $total_jam_lembur ?>&jml_mangkir=<?= $total_mangkir ?>&jml_izin=<?= $jml_izin ?>"
                                class="bg-green-500 text-white px-3 py-1.5 rounded text-xs hover:bg-green-600 shadow-sm transition">
                                ✅ Simpan & Slip
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalLembur" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="bg-indigo-600 p-4 flex justify-between items-center text-white">
            <h3 class="font-bold flex items-center gap-2">
                <i class="fas fa-clock"></i> Detail Lembur - <span id="modalNama"></span>
            </h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
        </div>
        <div class="p-6">
            <ul id="modalList" class="space-y-3 text-sm text-gray-600">
            </ul>
        </div>
        <div class="bg-gray-50 p-4 text-right">
            <button onclick="closeModal()"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-500">Tutup</button>
        </div>
    </div>
</div>

<script>
    function openModal(nama, detailJson) {
        const details = JSON.parse(detailJson);
        const listElement = document.getElementById('modalList');
        const namaElement = document.getElementById('modalNama');
        const modal = document.getElementById('modalLembur');

        namaElement.innerText = nama;
        listElement.innerHTML = ''; // Reset list

        if (details.length === 0) {
            listElement.innerHTML = '<li class="text-center italic text-gray-400">Tidak ada lembur yang tercatat.</li>';
        } else {
            details.forEach(item => {
                const li = document.createElement('li');
                li.className = "pb-2 border-b border-gray-100 flex justify-between";
                li.innerHTML = item;
                listElement.appendChild(li);
            });
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalLembur').classList.add('hidden');
    }

    // Tutup modal kalau klik di luar kotak putih
    window.onclick = function(event) {
        const modal = document.getElementById('modalLembur');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
<?php include 'footer.php'; ?>