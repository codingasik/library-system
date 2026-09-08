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