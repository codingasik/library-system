@props([
  'sortable' => false,
  'column' => null,
  'activeColumn' => null,
  'direction' => 'asc',
  'align' => 'left',
])

@php
  $alignClass = [
    'left' => 'text-left justify-start',
    'center' => 'text-center justify-center',
    'right' => 'text-right justify-end',
  ][$align] ?? 'text-left justify-start';
@endphp

<th 
  {{ $attributes->merge([
    'class' => "px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-500 " . 
               ($sortable ? 'cursor-pointer select-none hover:text-slate-700 transition' : '')
  ]) }}
  @if($sortable && $column) wire:click="sortBy('{{ $column }}')" @endif
>
  <div class="flex items-center gap-1.5 {{ $alignClass }}">
    <span>{{ $slot }}</span>

    @if ($sortable && $activeColumn === $column)
      <span class="text-indigo-600 font-bold">
        {{ $direction === 'asc' ? '↑' : '↓' }}
      </span>
    @endif
  </div>
</th>