{{-- resources/views/components/ui/badge.blade.php--}}
@props([
  'color' => 'gray',
  'size'  => 'md',
  'icon'  => null,
  'dot'   => false,
])

@php
  // 1. Variasi Warna & Border
  $colors = [
    'gray'   => 'border-slate-200 bg-slate-50 text-slate-600',
    'green'  => 'border-emerald-200 bg-emerald-50 text-emerald-700',
    'red'    => 'border-rose-200 bg-rose-50 text-rose-700',
    'blue'   => 'border-sky-200 bg-sky-50 text-sky-700',
    'indigo' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
    'yellow' => 'border-amber-200 bg-amber-50 text-amber-700',
  ];

  // 2. Warna Dot Indikator (Match dengan tema warna)
  $dots = [
    'gray'   => 'bg-slate-400',
    'green'  => 'bg-emerald-500',
    'red'    => 'bg-rose-500',
    'blue'   => 'bg-sky-500',
    'indigo' => 'bg-indigo-500',
    'yellow' => 'bg-amber-500',
  ];

  // 3. Variasi Ukuran
  $sizes = [
    'sm' => 'px-2 py-0.5 text-[11px] rounded-md gap-1',
    'md' => 'px-2.5 py-1 text-xs rounded-lg gap-1.5',
    'lg' => 'px-3 py-1.5 text-xs font-semibold rounded-xl gap-1.5',
  ];

  $colorClass = $colors[$color] ?? $colors['gray'];
  $dotClass   = $dots[$color] ?? $dots['gray'];
  $sizeClass  = $sizes[$size] ?? $sizes['md'];
@endphp

<span
  {{ $attributes->merge([
    'class' => "inline-flex items-center shrink-0 border font-medium transition $sizeClass $colorClass"
  ]) }}
>
  {{-- Dot Indikator --}}
  @if ($dot)
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
  @endif

  {{-- Dynamic Icon --}}
  @if ($icon)
    <x-dynamic-component :component="'lucide-' . $icon" class="w-3.5 h-3.5" />
  @endif

  {{ $slot }}
</span>