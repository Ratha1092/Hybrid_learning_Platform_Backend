import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// // Initialize sidebar as collapsed on desktop
// document.addEventListener('DOMContentLoaded', () => {
//     const initSidebarCollapsed = () => {
//         const sidebarToggle = document.querySelector('[data-testid="sidebar-toggle"]');
//         const sidebar = document.querySelector('.fi-sidebar');
        
//         if (sidebar && !sidebar.classList.contains('fi-sidebar-closed')) {
//             if (sidebarToggle) {
//                 sidebarToggle.click();
//             }
//         }
//     };
    
//     // Wait for Filament to fully load
//     setTimeout(initSidebarCollapsed, 500);
    
//     // Reinitialize on Alpine updates
//     Alpine.effect(() => {
//         setTimeout(initSidebarCollapsed, 100);
//     });
// });
