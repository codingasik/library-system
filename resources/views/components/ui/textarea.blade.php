{{-- resources/views/components/ui/textarea.blade.php--}}
@props([
  'name',
  'label' => null,
  'placeholder' => null,
  'rows' => 4,
  'hint' => null,
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

  {{-- Textarea Input --}}
  <textarea
    id="{{ $name }}"
    name="{{ $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->class([
      'w-full rounded-xl border px-3.5 py-2.5 text-sm outline-none transition resize-y',
      'border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100' => !$errors->has($name),
      'border-rose-400 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-4 focus:ring-rose-100' => $errors->has($name),
    ]) }}
  ></textarea>

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