<?php
require '../config.php';

// Cek sesi login admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link.active {
            background-color: #4f46e5;
            color: white;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="relative min-h-screen md:flex">
        <!-- Overlay untuk mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

        <!-- Sidebar -->
        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        ?>

        <aside id="sidebar"
            class="fixed inset-y-0 left-0 bg-slate-900 text-slate-300 w-72 transform -translate-x-full md:relative md:translate-x-0 transition-all duration-300 ease-in-out z-30 shadow-2xl border-r border-slate-800 flex flex-col">

            <div class="p-8">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 p-2 rounded-xl shadow-lg shadow-indigo-500/30 flex-shrink-0">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h1 class="text-white text-lg font-extrabold tracking-tight truncate">Admin Panel</h1>
                        <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest truncate">Absensi &
                            Penggajian</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar pb-24">

                <div class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Utama</div>

                <a href="dashboard.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'dashboard.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-th-large w-6 text-center text-lg <?= $current_page == 'dashboard.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Dashboard</span>
                </a>

                <div class="px-4 py-2 mt-6 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Manajemen
                    Data</div>

                <a href="karyawan.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'karyawan.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-user-group w-6 text-center text-lg <?= $current_page == 'karyawan.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Data Karyawan</span>
                </a>

                <a href="kelola_jadwal.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'kelola_jadwal.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-calendar-alt w-6 text-center text-lg <?= $current_page == 'kelola_jadwal.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Kelola Jadwal</span>
                </a>

                <a href="kelola_absen.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'kelola_absen.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-file-signature w-6 text-center text-lg <?= $current_page == 'kelola_absen.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Kelola Absensi</span>
                </a>

                <div class="px-4 py-2 mt-6 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Absensi &
                    Izin</div>

                <a href="riwayat.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'riwayat.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-clock-rotate-left w-6 text-center text-lg <?= $current_page == 'riwayat.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Riwayat Absensi</span>
                </a>

                <a href="izin_keluar.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'izin_keluar.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-door-open w-6 text-center text-lg <?= $current_page == 'izin_keluar.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Izin Keluar</span>
                </a>

                <div class="px-4 py-2 mt-6 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Penggajian
                </div>

                <a href="generate_gaji.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
            <?= $current_page == 'generate_gaji.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i
                        class="fas fa-money-check-dollar w-6 text-center text-lg <?= $current_page == 'generate_gaji.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Generate Gaji</span>
                </a>


                <div class="px-4 py-2 mt-6 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Laporan
                </div>

                <a href="laporan_data_karyawan.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
    <?= $current_page == 'laporan_data_karyawan.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fas fa-id-card w-6 text-center text-lg 
        <?= $current_page == 'laporan_data_karyawan.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Laporan Data Karyawan</span>
                </a>

                <a href="laporan_penggajian.php"
                    class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 
    <?= $current_page == 'laporan_penggajian.php' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'hover:bg-slate-800 hover:text-white' ?>">
                    <i class="fas fa-file-invoice-dollar w-6 text-center text-lg 
        <?= $current_page == 'laporan_penggajian.php' ? '' : 'text-slate-500 group-hover:text-indigo-400' ?>"></i>
                    <span class="font-semibold text-sm">Laporan Data Penggajian</span>
                </a>



            </nav>

            <div class="p-6 border-t border-slate-800 bg-slate-900/50 backdrop-blur-md">
                <a href="logout.php"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-red-500/10 hover:text-red-500 transition-all duration-200 font-bold text-sm">
                    <i class="fas fa-arrow-right-from-bracket w-6 text-center"></i>
                    <span>Keluar Sistem</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            <header class="bg-white shadow-md p-4 flex justify-between items-center">
                <!-- Tombol Hamburger (hanya tampil di mobile) -->
                <button id="sidebar-toggle" class="text-gray-500 focus:outline-none md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-xl font-semibold text-gray-700"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                <div class="text-gray-600">
                    Selamat datang, <span class="font-bold"><?php echo htmlspecialchars($admin_username); ?></span>
                </div>
            </header>
            <div class="p-6 overflow-y-auto">