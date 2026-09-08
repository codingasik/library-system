// resources/js/app.js
import './bootstrap';

document.addEventListener('livewire:navigated', () => {
  // Inisialisasi ulang jika menggunakan SPA wire:navigate
  initSidebar();
});

document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
});

function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarOverlay = document.getElementById('sidebar-overlay');
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const contentWrapper = document.getElementById('content-wrapper');
  const sidebarTexts = document.querySelectorAll('.sidebar-text');
  const navLinks = document.querySelectorAll('.nav-link');

  if (!sidebarToggle) return;

  sidebarToggle.onclick = () => {
    if (window.innerWidth >= 1024) {
      sidebar.classList.toggle('w-60');
      sidebar.classList.toggle('w-20');
      contentWrapper.classList.toggle('lg:ml-60');
      contentWrapper.classList.toggle('lg:ml-20');

      navLinks.forEach(link => link.classList.toggle('justify-center'));
      sidebarTexts.forEach(text => text.classList.toggle('hidden'));
    } else {
      sidebar.classList.remove('-translate-x-full');
      sidebarOverlay.classList.remove('hidden');
    }
  };

  sidebarOverlay.onclick = () => {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
  };

  navLinks.forEach(link => {
    link.onclick = () => {
      if (window.innerWidth < 1024) {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
      }
    };
  });
}