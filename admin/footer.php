    </div>
    </main>
    </div>
    <script>
        // Menunggu sampai seluruh dokumen HTML selesai dimuat oleh browser
        document.addEventListener('DOMContentLoaded', function() {

            // --- Script untuk menandai link sidebar yang aktif ---
            const currentPage = window.location.pathname.split('/').pop();
            const links = document.querySelectorAll('.sidebar-link');
            links.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });

            // --- SCRIPT UNTUK TOGGLE SIDEBAR RESPONSIVE ---
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            // Fungsi untuk menampilkan/menyembunyikan sidebar
            function toggleSidebar() {
                if (sidebar && overlay) { // Pastikan elemennya ada
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                }
            }

            // Tambahkan event listener HANYA JIKA tombolnya ditemukan
            if (toggleButton) {
                toggleButton.addEventListener('click', toggleSidebar);
            }

            // Tambahkan event listener HANYA JIKA overlaynya ditemukan
            if (overlay) {
                overlay.addEventListener('click', toggleSidebar);
            }

        });
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script>
        new DataTable('#karyawanTable', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#absenTable', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#gajiTable', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#daftarKaryawanTable', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#riwayatTable', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#monitoringTable', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#tabelLaporanKaryawan', {
            responsive: true
        });
    </script>
    <script>
        new DataTable('#tabelLaporanGaji', {
            responsive: true
        });
    </script>


    <script>
        $(document).ready(function() {
            // Aktifkan Select2
            $('.select2-js').select2({
                width: '100%'
            });

            // Aktifkan DataTables
            $('#jadwalTable').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "Cari Jadwal:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ jadwal",
                    "paginate": {
                        "next": "Lanjut",
                        "previous": "Kembali"
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#filterKaryawan').select2({
                placeholder: "Cari Nama...",
                allowClear: true,
                width: '100%' // Memastikan lebar mengikuti grid Tailwind
            });
        });
    </script>

    <script>
        function exportTableToExcel(tableID, filename = '') {
            // 1. Ambil elemen tabel
            var table = document.getElementById(tableID);

            // 2. Konversi tabel HTML ke Worksheet
            var wb = XLSX.utils.table_to_book(table, {
                sheet: "Laporan"
            });

            // 3. Nama file default jika kosong
            filename = filename ? filename + '.xlsx' : 'laporan_export.xlsx';

            // 4. Proses Download
            XLSX.writeFile(wb, filename);
        }
    </script>
    </body>

    </html>