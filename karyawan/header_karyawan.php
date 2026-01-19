<?php
session_start();
require '../config.php';

// Proteksi: Jika bukan karyawan atau belum login, tendang ke login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: ../login.php");
    exit();
}

$nama_user = $_SESSION['nama_karyawan'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title; ?> - Panel Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-slate-50 font-sans text-slate-900">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-indigo-700 text-white hidden md:block">
            <div class="p-6">
                <h1 class="text-xl font-bold tracking-tight italic">E-Payroll</h1>
                <p class="text-[10px] text-indigo-200 uppercase tracking-widest mt-1">Employee Portal</p>
            </div>

            <nav class="mt-6 px-4 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-600 transition">
                    <i class="fas fa-home w-5 text-center"></i> Dashboard
                </a>
                <a href="riwayat_gaji.php" class="flex items-center gap-3 p-3 rounded-lg bg-indigo-800 shadow-inner">
                    <i class="fas fa-file-invoice-dollar w-5 text-center"></i> Slip Gaji Saya
                </a>
                <hr class="border-indigo-500 my-4">
                <a href="../logout.php"
                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-red-600 transition text-red-100">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i> Keluar
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800"><?= $page_title; ?></h1>
                    <p class="text-sm text-slate-500">Selamat datang kembali, <strong><?= $nama_user; ?></strong></p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-700"><?= $nama_user; ?></p>
                        <p class="text-[10px] text-slate-400">ID: <?= $_SESSION['id_karyawan']; ?></p>
                    </div>
                    <div
                        class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold border-2 border-indigo-200">
                        <?= substr($nama_user, 0, 1); ?>
                    </div>
                </div>
            </header>