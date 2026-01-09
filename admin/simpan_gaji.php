<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../config.php';

if (isset($_GET['id']) && isset($_GET['bulan']) && isset($_GET['tahun'])) {
    $id_k = sanitize($koneksi, $_GET['id']);
    $bulan = sanitize($koneksi, $_GET['bulan']);
    $tahun = sanitize($koneksi, $_GET['tahun']);

    // 1. Ambil Data Master Karyawan
    $k = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM karyawan WHERE id='$id_k'"));
    if (!$k) die("Data karyawan tidak ditemukan.");

    // 2. Setting Tarif
    $tarif_makan = 3000;
    $tarif_transport = 2000;
    $tarif_lembur = 21590;
    $pot_mangkir_per_hari = 83000;
    $tarif_izin_keluar_per_jam = 11857;
    $tarif_pot_insentif = 2667;
    $tarif_pot_prestasi = 2667;

    // A. Hitung Kehadiran
    $res_hadir = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE id_karyawan='$id_k' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun' AND status='keluar'");
    $total_hadir = mysqli_fetch_assoc($res_hadir)['total'];

    // B. REVISI: Hitung Lembur (Logika Aman Tengah Malam)
    $total_jam_lembur = 0;
    $res_lembur = mysqli_query($koneksi, "SELECT a.jam_keluar, s.jam_keluar as jam_seharusnya 
                                          FROM absensi a 
                                          JOIN jadwal_kerja jk ON a.id_jadwal = jk.id 
                                          JOIN shifts s ON jk.id_shift = s.id 
                                          WHERE a.id_karyawan='$id_k' AND MONTH(a.tanggal)='$bulan' AND YEAR(a.tanggal)='$tahun'");

    // while ($l = mysqli_fetch_assoc($res_lembur)) {
    //     $jam_pulang_absen = strtotime($l['jam_keluar']);
    //     $jam_pulang_seharusnya = strtotime($l['jam_seharusnya']);

    //     // Logika Lembur Lewat Tengah Malam
    //     if ($jam_pulang_absen < $jam_pulang_seharusnya && $l['jam_keluar'] != '00:00:00') {
    //         // Jika jam pulang < jam seharusnya (misal 01:00 < 23:00), tambah 24 jam (86400 detik)
    //         $diff = ($jam_pulang_absen + 86400) - $jam_pulang_seharusnya;
    //     } else {
    //         $diff = $jam_pulang_absen - $jam_pulang_seharusnya;
    //     }

    //     if ($diff > 0) {
    //         $menit_lembur = floor($diff / 60);
    //         if ($menit_lembur >= 30) {
    //             $total_jam_lembur += floor($menit_lembur / 60);
    //         }
    //     }
    // }
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

    // C. Hitung Mangkir
    // $res_mangkir = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_kerja jk LEFT JOIN absensi a ON jk.id_karyawan = a.id_karyawan AND jk.tanggal = a.tanggal WHERE jk.id_karyawan='$id_k' AND jk.id_shift != 3 AND MONTH(jk.tanggal)='$bulan' AND YEAR(jk.tanggal)='$tahun' AND a.id IS NULL");
    // $total_mangkir = mysqli_fetch_assoc($res_mangkir)['total'];
    $res_mangkir = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_kerja jk 
                                       LEFT JOIN absensi a ON jk.id_karyawan = a.id_karyawan AND jk.tanggal = a.tanggal 
                                       WHERE jk.id_karyawan='$id_k' 
                                       AND jk.id_shift != 3 
                                       AND DAYOFWEEK(jk.tanggal) != 1 
                                       AND MONTH(jk.tanggal)='$bulan' 
                                       AND YEAR(jk.tanggal)='$tahun' 
                                       AND a.id IS NULL");
    $total_mangkir = mysqli_fetch_assoc($res_mangkir)['total'];

    // D. Hitung Izin Keluar
    $sql_izin = "SELECT SUM(HOUR(TIMEDIFF(jam_kembali, jam_pergi))) as total_jam FROM izin_keluar WHERE id_karyawan = '$id_k' AND MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun' AND status_izin = 'kembali'";
    $data_izin = mysqli_fetch_assoc(mysqli_query($koneksi, $sql_izin));
    $total_jam_izin = $data_izin['total_jam'] ?? 0;

    // 3. Kalkulasi Finansial
    $gaji_pokok   = $k['gaji_pokok'];
    $sub_makan    = $total_hadir * $tarif_makan;
    $sub_transport = $total_hadir * $tarif_transport;
    $total_lembur_nominal = $total_jam_lembur * $tarif_lembur;

    $pot_mangkir_total  = $total_mangkir * $pot_mangkir_per_hari;
    $pot_izin_keluar    = $total_jam_izin * $tarif_izin_keluar_per_jam;
    $pot_insentif       = $total_mangkir * $tarif_pot_insentif;
    $pot_prestasi       = $total_mangkir * $tarif_pot_prestasi;

    $total_pendapatan = $gaji_pokok + $k['tunjangan_tetap'] + $sub_makan + $sub_transport + $total_lembur_nominal;
    $total_potongan   = $pot_mangkir_total + $pot_izin_keluar + $pot_insentif + $pot_prestasi;

    $gaji_bersih  = $total_pendapatan - $total_potongan;
    $tgl_generate = date('Y-m-d H:i:s');

    // 4. Proses Simpan ke Tabel Penggajian
    $cek = mysqli_query($koneksi, "SELECT id FROM penggajian WHERE id_karyawan='$id_k' AND bulan='$bulan' AND tahun='$tahun'");

    if (mysqli_num_rows($cek) > 0) {
        $sql_simpan = "UPDATE penggajian SET 
                       total_gaji_pokok = '$gaji_pokok', subsidi_makan = '$sub_makan', subsidi_transport = '$sub_transport', 
                       lembur = '$total_lembur_nominal', insentif = '$pot_insentif', prestasi = '$pot_prestasi', 
                       potongan_mangkir_sakit = '$pot_mangkir_total', total_terima = '$gaji_bersih', 
                       jml_makan = '$total_hadir', jml_transport = '$total_hadir', jml_mangkir = '$total_mangkir', 
                       jml_lembur_1 = '$total_jam_lembur', jml_izin_keluar = '$total_jam_izin', 
                       tanggal_generate = '$tgl_generate'
                       WHERE id_karyawan='$id_k' AND bulan='$bulan' AND tahun='$tahun'";
    } else {
        $sql_simpan = "INSERT INTO penggajian 
                       (id_karyawan, bulan, tahun, total_gaji_pokok, subsidi_makan, subsidi_transport, lembur, insentif, prestasi, potongan_mangkir_sakit, total_terima, jml_makan, jml_transport, jml_mangkir, jml_lembur_1, jml_izin_keluar, tanggal_generate) 
                       VALUES 
                       ('$id_k', '$bulan', '$tahun', '$gaji_pokok', '$sub_makan', '$sub_transport', '$total_lembur_nominal', '$pot_insentif', '$pot_prestasi', '$pot_mangkir_total', '$gaji_bersih', '$total_hadir', '$total_hadir', '$total_mangkir', '$total_jam_lembur', '$total_jam_izin', '$tgl_generate')";
    }

    if (mysqli_query($koneksi, $sql_simpan)) {
        header("Location: cetak_slip.php?id_karyawan=$id_k&bulan=$bulan&tahun=$tahun");
    } else {
        echo "Gagal menyimpan gaji: " . mysqli_error($koneksi);
    }
}
