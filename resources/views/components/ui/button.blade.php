{{-- resources/views/components/ui/button.blade.php--}}
@props([
  'variant' => 'primary',
  'size'    => 'md',
  'type'    => 'button',
  'href'    => null,
])

@php
  // 1. Variasi Warna (Support CRUD & Actions)
  $variants = [
    'primary'   => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm shadow-indigo-100',
    'secondary' => 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 focus:ring-slate-300',
    'success'   => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-sm shadow-emerald-100',
    'danger'    => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500 shadow-sm shadow-rose-100',
    'ghost'     => 'bg-transparent text-slate-600 hover:bg-slate-100 focus:ring-slate-300',
  ];

  // 2. Variasi Ukuran
  $sizes = [
    'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5',
    'md' => 'px-4 py-2 text-sm rounded-xl gap-2',
    'lg' => 'px-5 py-2.5 text-base rounded-2xl gap-2.5',
  ];

  $variantClass = $variants[$variant] ?? $variants['primary'];
  $sizeClass    = $sizes[$size] ?? $sizes['md'];
  $baseClass    = 'inline-flex items-center justify-center font-medium transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed';
@endphp

@if ($href)
  {{-- Jika ada prop href, render sebagai Link (SPA-ready dengan wire:navigate) --}}
  <a href="{{ $href }}" wire:navigate {{ $attributes->merge(['class' => "$baseClass $variantClass $sizeClass"]) }}>
      {{ $slot }}
  </a>
@else
  {{-- Jika tidak ada href, render sebagai Button bawaan --}}
  <button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClass $variantClass $sizeClass"]) }}>
      {{ $slot }}
  </button>
@endif