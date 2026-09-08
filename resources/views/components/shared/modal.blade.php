@props([
  'title' => 'Modal',
  'description' => null
])

{{-- Container Utama Modal --}}
{{-- wire:keydown.escape.window="closeModal" menangani tombol ESC keyboard --}}
<div 
  wire:keydown.escape.window="closeModal" 
  class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
>
  {{-- 1. BACKDROP / OVERLAY (Klik di luar modal untuk menutup) --}}
  <div 
    wire:click="closeModal" 
    class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm transition-opacity"
  ></div>

  {{-- 2. MODAL DIALOG CONTAINER --}}
  <div class="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-100">

    {{-- Modal Header --}}
    <div class="flex items-start justify-between border-b border-slate-100 px-6 py-5 bg-white">
      <div>
        <h2 class="text-lg font-bold text-slate-800">
          {{ $title }}
        </h2>
        @if($description)
          <p class="mt-1 text-sm text-slate-500">
            {{ $description }}
          </p>
        @endif
      </div>

      {{-- Tombol Close X --}}
      @if (isset($close))
        {{ $close }}
      @else
        <button
          type="button"
          wire:click="closeModal"
          class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
        >
          ✕
        </button>
      @endif
    </div>

    {{-- Modal Body / Form --}}
    {{ $slot }}

  </div>
</div>