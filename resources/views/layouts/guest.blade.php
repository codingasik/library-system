<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Library System'}}</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800">
  <main class="min-h-full flex flex-col justify-center">
    {{ $slot }}
  </main>
</body>
</html>