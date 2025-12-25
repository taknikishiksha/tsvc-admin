<script>
    // Mobile Menu Toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        document.querySelector('.mobile-menu').classList.add('active');
        document.querySelector('.overlay').classList.add('active');
    });

    document.getElementById('close-mobile-menu').addEventListener('click', function() {
        document.querySelector('.mobile-menu').classList.remove('active');
        document.querySelector('.overlay').classList.remove('active');
    });

    document.querySelector('.overlay').addEventListener('click', function() {
        document.querySelector('.mobile-menu').classList.remove('active');
        document.querySelector('.overlay').classList.remove('active');
    });

    // Mobile Submenu Toggle
    document.querySelectorAll('.mobile-menu-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const submenu = this.nextElementSibling;
            const isActive = this.classList.contains('active');
            
            // Close all other submenus
            document.querySelectorAll('.mobile-submenu').forEach(menu => {
                menu.classList.remove('active');
            });
            document.querySelectorAll('.mobile-menu-toggle').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Toggle current submenu
            if (!isActive) {
                this.classList.add('active');
                submenu.classList.add('active');
            }
        });
    });

    // Close mobile submenus when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            const isClickInsideMenu = document.querySelector('.mobile-menu').contains(event.target);
            const isMenuButton = document.getElementById('mobile-menu-button').contains(event.target);
            
            if (!isClickInsideMenu && !isMenuButton) {
                document.querySelectorAll('.mobile-submenu').forEach(menu => {
                    menu.classList.remove('active');
                });
                document.querySelectorAll('.mobile-menu-toggle').forEach(btn => {
                    btn.classList.remove('active');
                });
            }
        }
    });
</script>