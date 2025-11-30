// Admin Sidebar JavaScript

// Toggle sidebar on mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
    if (mainContent) {
        mainContent.classList.toggle('expanded');
    }
}

// Toggle dropdown menus
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');

    if (parent) {
        // Close other open dropdowns
        document.querySelectorAll('.dropdown.active').forEach(dropdown => {
            if (dropdown !== parent) {
                dropdown.classList.remove('active');
            }
        });

        // Toggle current dropdown
        parent.classList.toggle('active');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.active').forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    }
});

// Highlight active menu item based on current URL
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.menu-item, .submenu-item');

    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href)) {
            item.classList.add('active');

            // If it's a submenu item, open the parent dropdown
            const parentDropdown = item.closest('.dropdown');
            if (parentDropdown) {
                parentDropdown.classList.add('active');
            }
        }
    });
});
