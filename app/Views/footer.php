    </main>
    
    <?php if (!empty($usuario)): ?>
                </main>
            </div>
        </div>
    <?php else: ?>
    <?php endif; ?>
    
    <footer role="contentinfo" style="text-align: center; padding: 2rem; background: var(--bg-primary); border-top: 1px solid var(--border-color);">
        <p>&copy; <?= date('Y') ?> Sistema de Escola de Esportes. Todos os direitos reservados.</p>
    </footer>
    
    <!-- JavaScript Local -->
    <script src="<?= ASSETS_URL ?>/js/utils.js"></script>
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
    <?php if (strpos($_SERVER['REQUEST_URI'] ?? '', '/alunos') !== false): ?>
    <script src="<?= ASSETS_URL ?>/js/datatables-simple.js"></script>
    <?php endif; ?>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
        
        // Fecha sidebar ao clicar em link no mobile
        document.querySelectorAll('.sidebar-menu-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
            });
        });
        
        // Toggle menu do usuário
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }
        
        // Fecha menu do usuário ao clicar fora
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.top-user-menu');
            const dropdown = document.getElementById('userDropdown');
            if (userMenu && !userMenu.contains(event.target) && dropdown.classList.contains('active')) {
                dropdown.classList.remove('active');
            }
        });
    </script>
</body>
</html>

