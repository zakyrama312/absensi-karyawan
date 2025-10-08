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
        new DataTable('#riwayatTable', {
            responsive: true
        });
    </script>
    </body>

    </html>