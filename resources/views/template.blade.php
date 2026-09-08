<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>@yield('title', 'Library System')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body class="h-full bg-gray-50 text-gray-800">

  {{-- Mobile Sidebar Overlay --}}
  <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

  {{-- Wrapper Utama --}}
  <div class="min-h-screen">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-60 -translate-x-full flex-col bg-slate-900 text-white transition-all duration-200 lg:translate-x-0">

      {{-- Logo --}}
      <div id="sidebar-logo" class="flex items-center gap-3 border-b border-slate-800 px-5 py-5">
        <span class="shrink-0 text-indigo-400">
          <x-lucide-book-open class="h-6 w-6" />
        </span>

        <div class="sidebar-text">
          <p class="text-sm font-bold leading-tight">Library System</p>
          <p class="text-xs text-slate-400">Management App</p>
        </div>
      </div>

      {{-- Navigasi --}}
      <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">

        @php
          $navItems = [
            ['icon' => 'lucide-layout-dashboard', 'label' => 'Dashboard', 'route' => 'dashboard'],
            ['icon' => 'lucide-book',             'label' => 'Books',     'route' => 'books.index'],
            ['icon' => 'lucide-users',            'label' => 'Members',   'route' => 'members.index'],
            ['icon' => 'lucide-repeat',           'label' => 'Loans',     'route' => 'loans.index'],
            ['icon' => 'lucide-tags',             'label' => 'Categories','route' => 'categories.index'],
          ];
        @endphp

        @foreach ($navItems as $item)
          <a
            href="{{ route($item['route']) }}"
            wire:navigate
            title="{{ $item['label'] }}"
            class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors
              {{ request()->routeIs($item['route'])
                ? 'bg-indigo-600 font-semibold text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">

            <x-dynamic-component :component="$item['icon']" class="h-5 w-5 shrink-0" />

            <span class="sidebar-text whitespace-nowrap">
              {{ $item['label'] }}
            </span>
          </a>
        @endforeach
      </nav>

      {{-- Footer Sidebar --}}
      <div class="sidebar-footer shrink-0 border-t border-slate-800 px-5 py-4 text-xs text-slate-500">
        <span class="sidebar-text whitespace-nowrap">
          Library System v1.0
        </span>
      </div>
    </aside>

    {{-- AREA KONTEN --}}
    <div id="content-wrapper" class="flex min-h-screen min-w-0 flex-1 flex-col transition-all duration-200 lg:ml-60">

      {{-- Topbar --}}
      <header class="sticky top-0 z-30 flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3.5 md:px-6">

        {{-- Sidebar Toggle Button --}}
        <button
          id="sidebar-toggle"
          class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100"
          aria-label="Toggle sidebar">
          <x-lucide-menu class="h-5 w-5" />
        </button>

        {{-- Date --}}
        <div class="hidden items-center gap-2 text-xs text-gray-500 sm:flex">
          <x-lucide-calendar class="h-4 w-4 text-gray-400" />
          {{ now()->format('d M Y') }}
        </div>
      </header>

      {{-- Konten Halaman --}}
      <main class="flex-1 p-4 md:p-6">
        @yield('content')
      </main>

    </div>
  </div>

  @livewireScripts

  <script>
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
  </script>

</body>
</html>
