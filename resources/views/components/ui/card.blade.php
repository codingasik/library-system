{{-- resources/views/components/ui/card.blade.php--}}
@props([
  'title' => null,
  'description' => null,
  'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl border border-slate-100 shadow-sm $padding"]) }}>
  {{-- Header Section (Hanya tampil jika ada title, description, atau slot actions) --}}
  @if ($title || $description || isset($actions))
    <div class="flex items-start justify-between gap-4 mb-5">
      <div>
        @if ($title)
          <h3 class="font-semibold text-base text-slate-800 tracking-tight">{{ $title }}</h3>
        @endif
        @if ($description)
          <p class="text-xs text-slate-500 mt-0.5">{{ $description }}</p>
        @endif
      </div>

      {{-- Slot untuk Tombol/Aksi di Pojok Kanan Atas --}}
      @isset($actions)
        <div class="flex items-center gap-2 shrink-0">
          {{ $actions }}
        </div>
      @endisset
    </div>
  @endif

  {{-- Content Body --}}
  {{ $slot }}
</div>