{{-- resources/views/components/ui/select.blade.php--}}
@props([
  'name',
  'label' => null,
  'placeholder' => 'Pilih data',
  'hint' => null,
  'icon' => null,
  'showError' => true,
])

<div>
  {{-- Label Form --}}
  @if ($label)
    <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
      {{ $label }}
      @if ($attributes->has('required'))
        <span class="text-rose-500">*</span>
      @endif
    </label>
  @endif

  {{-- Select Wrapper --}}
  <div class="relative">
    {{-- Icon Kiri (Optional) --}}
    @if ($icon)
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
      </div>
    @endif

    <select
      id="{{ $name }}"
      name="{{ $name }}"
      {{ $attributes->class([
        'w-full appearance-none rounded-xl border bg-white text-sm outline-none transition',
        'py-2.5',
        'pl-10 pr-10' => $icon,
        'pl-3.5 pr-10' => !$icon,
        'border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100' => !$errors->has($name),
        'border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-100' => $errors->has($name),
      ]) }}
    >
      @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
      @endif

      {{ $slot }}
    </select>

    {{-- Icon Chevron Panah Kanan Custom --}}
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
      <x-lucide-chevron-down class="w-4 h-4" />
    </div>
  </div>

  {{-- Error Message & Hint --}}
  @if ($showError && $errors->has($name))
    @error($name)
      <p class="mt-1.5 text-xs font-medium text-rose-500 flex items-center gap-1">
        <x-lucide-alert-circle class="w-3.5 h-3.5" />
        <span>{{ $message }}</span>
      </p>
    @enderror
  @elseif ($hint)
    <p class="mt-1.5 text-xs text-slate-400">
      {{ $hint }}
    </p>
  @endif
</div>