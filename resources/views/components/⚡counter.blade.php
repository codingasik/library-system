<?php

use Livewire\Component;

new class extends Component {
  public $count = 0;

  // Tambahkan ini
  public function mount($start = 0)
  {
    $this->count = $start;
  }

  public function increment($step = 1)
  {
    $this->count += $step;
  }

  public function decrement($step = 1)
  {
    if ($this->count > 0) {
      $this->count -= $step;
    }
  }

  public function resetCount()
  {
    $this->reset('count');
    // Tambahkan ini
    $this->dispatch('notify', message: 'Counter direset ke 0!');
  }
}; ?>

<div class="p-6 mx-auto max-w-xs text-center bg-white border border-gray-100 rounded-2xl shadow-sm">
  <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Counter Simple</p>

  {{-- Angka Counter --}}
  <h1 class="my-3 text-5xl font-extrabold text-indigo-600 tracking-tight transition-all">
    {{ $count }}
  </h1>

  {{-- Tombol Aksi --}}
  <div class="flex gap-2 justify-center items-center mt-4">

    <x-ui.button 
      variant="secondary" 
      size="md"
      wire:click="decrement"
      :disabled="$count <= 0"
      class="py-3">
      <x-lucide-minus class="w-5 h-5" />
    </x-ui.button>

    <x-ui.button 
      variant="success" 
      size="md"
      wire:click="resetCount"
      :disabled="$count <= 0"
      class="py-3">
      <x-lucide-rotate-ccw class="w-5 h-5" />
    </x-ui.button>

    <x-ui.button 
      variant="primary" 
      size="md"
      wire:click="increment"
      class="py-3">
      <x-lucide-plus class="w-5 h-5" /> 1
    </x-ui.button>

    <x-ui.button 
      variant="primary" 
      size="md"
      wire:click="increment(5)"
      class="py-3">
      <x-lucide-plus class="w-5 h-5" /> 5
    </x-ui.button>
  </div>
</div>