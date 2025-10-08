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
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
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
                <h1 class="text-xl font-semibold text-gray-700"><?php echo $page_title ?? 'Dashboard'; ?></h1>
                <div class="text-gray-600">
                    Selamat datang, <span class="font-bold"><?php echo htmlspecialchars($admin_username); ?></span>
                </div>
            </header>
            <div class="p-6 overflow-y-auto">