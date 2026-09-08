{{-- resources/views/components/ui/input.blade.php--}}
@props([
  'name',
  'label' => null,
  'type' => 'text',
  'placeholder' => null,
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

  {{-- Input Wrapper (Untuk Ikon Di Dalam Input) --}}
  <div class="relative">
    @if ($icon)
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
        <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" />
      </div>
    @endif

    <input
      id="{{ $name }}"
      name="{{ $name }}"
      type="{{ $type }}"
      placeholder="{{ $placeholder }}"
      {{ $attributes->class([
        'w-full rounded-xl border text-sm outline-none transition',
        'py-2.5',
        'pl-10 pr-3.5' => $icon,
        'px-3.5' => !$icon,
        'border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100' => !$errors->has($name),
        'border-rose-400 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-4 focus:ring-rose-100' => $errors->has($name),
      ]) }}
    />
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