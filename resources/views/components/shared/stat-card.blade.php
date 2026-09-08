@props([
  'title',
  'value' => 0,
  'description' => null,
  'color' => 'indigo', // Opsi warna aksen
])

@php
  $colorClasses = [
    'indigo' => 'bg-indigo-50 text-indigo-600',
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'amber'  => 'bg-amber-50 text-amber-600',
    'rose'   => 'bg-rose-50 text-rose-600',
    'slate'  => 'bg-slate-100 text-slate-600',
  ][$color] ?? 'bg-indigo-50 text-indigo-600';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md']) }}>
  <div class="flex items-start justify-between gap-4">
    <div class="space-y-1">
      <p class="text-sm font-medium text-slate-500">
        {{ $title }}
      </p>

      <h3 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
        {{ is_numeric($value) ? number_format($value) : $value }}
      </h3>

      @if ($description)
        <p class="text-xs text-slate-400">
          {{ $description }}
        </p>
      @endif
    </div>

    @if (isset($icon))
      <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $colorClasses }}">
        {{ $icon }}
      </div>
    @endif
  </div>
</div>