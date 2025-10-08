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
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 bg-gray-800 text-white w-64 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-200 ease-in-out z-30">
            <div class="p-4 text-2xl font-bold border-b border-gray-700">
                Admin Panel
            </div>
            <nav class="mt-4">
                <a href="dashboard.php" class="sidebar-link flex items-center p-4 hover:bg-gray-700">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>
                <a href="riwayat.php" class="sidebar-link flex items-center p-4 hover:bg-gray-700">
                    <i class="fas fa-history w-6"></i>
                    <span>Riwayat Absensi</span>
                </a>
                <a href="karyawan.php" class="sidebar-link flex items-center p-4 hover:bg-gray-700">
                    <i class="fas fa-users w-6"></i>
                    <span>Data Karyawan</span>
                </a>
                <a href="kelola_absen.php" class="sidebar-link flex items-center p-4 hover:bg-gray-700">
                    <i class="fas fa-edit w-6"></i>
                    <span>Kelola Absen</span>
                </a>
                <a href="logout.php" class="sidebar-link flex items-center p-4 hover:bg-red-600 mt-10">
                    <i class="fas fa-sign-out-alt w-6"></i>
                    <span>Logout</span>
                </a>
            </nav>
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