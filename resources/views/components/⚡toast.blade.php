<?php
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
  public $message = null;
  public $type = 'success'; // Pilihan: 'success' atau 'error'
  public $visible = false;

  #[On('notify')]
  public function show($message, $type = 'success')
  {
    $this->message = $message;
    $this->type = $type;
    $this->visible = true;
  }

  public function close()
  {
    $this->visible = false;
  }
}; ?>

<div>
  @if ($visible)
    {{-- wire:poll.3s akan memanggil close() otomatis setelah 3 detik --}}
    <div 
      wire:poll.3s="close"
      class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 bg-slate-900 text-white rounded-2xl shadow-xl border border-slate-800"
    >
      {{-- Ikon & Aksen Warna Dinamis --}}
      @if ($type === 'error')
        <div class="p-1.5 bg-rose-500/10 text-rose-400 rounded-xl">
          <x-lucide-alert-circle class="w-5 h-5" />
        </div>
      @else
        <div class="p-1.5 bg-emerald-500/10 text-emerald-400 rounded-xl">
          <x-lucide-check-circle-2 class="w-5 h-5" />
        </div>
      @endif

      {{-- Pesan Toast --}}
      <span class="text-xs font-medium text-slate-200">{{ $message }}</span>

      {{-- Tombol Close --}}
      <button 
        wire:click="close" 
        type="button"
        class="p-1 text-slate-400 hover:text-white rounded-lg transition cursor-pointer"
      >
        <x-lucide-x class="w-4 h-4" />
      </button>
    </div>
  @endif
</div>