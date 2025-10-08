            </div>
            </main>
            </div>
            <script>
const currentPage = window.location.pathname.split('/').pop();
const links = document.querySelectorAll('.sidebar-link');
links.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
        link.classList.add('active');
    }
});
            </script>
            </body>

            </html>