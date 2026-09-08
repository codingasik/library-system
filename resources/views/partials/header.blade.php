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