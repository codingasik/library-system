<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>{{ $title ?? 'Library System'}}</title>

  {{-- FAVICON LUCIDE --}}
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body class="h-full bg-gray-50 text-gray-800">

  {{-- Mobile Sidebar Overlay --}}
  <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>

  {{-- Wrapper Utama --}}
  <div class="min-h-screen">

    {{-- SIDEBAR --}}
    @include('partials.sidebar')

    {{-- AREA KONTEN --}}
    <div id="content-wrapper" class="flex min-h-screen min-w-0 flex-1 flex-col transition-all duration-200 lg:ml-60">

      {{-- Topbar --}}
      @include('partials.header')

      {{-- Konten Halaman --}}
      <main class="flex-1 p-4 md:p-6">
        {{-- Ubah jadi $slot di Livewire --}}
        {{ $slot }}
      </main>

      {{-- Tambahkan component Toast agar bisa diakses disemua halaman --}}
      <livewire:toast />

    </div>
  </div>

  @livewireScripts

</body>
</html>
