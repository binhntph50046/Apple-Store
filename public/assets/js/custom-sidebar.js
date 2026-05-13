document.addEventListener('DOMContentLoaded', function() {
    // Override default sidebar behavior
    const menuItems = document.querySelectorAll('.pc-hasmenu');
    
    menuItems.forEach(item => {
        const link = item.querySelector('.pc-link');
        const submenu = item.querySelector('.pc-submenu');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle current menu
            if (item.classList.contains('pc-trigger')) {
                item.classList.remove('pc-trigger');
                submenu.style.display = 'none';
            } else {
                item.classList.add('pc-trigger');
                submenu.style.display = 'block';
            }
            
            // Rotate arrow icon
            const arrow = this.querySelector('.pc-arrow i');
            if (arrow) {
                arrow.style.transform = item.classList.contains('pc-trigger') ? 'rotate(90deg)' : 'rotate(0deg)';
            }
        });
    });

    // Prevent submenu items from triggering parent menu toggle
    const submenuLinks = document.querySelectorAll('.pc-submenu .pc-link');
    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
}); 