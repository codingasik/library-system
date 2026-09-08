<?php

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Component;
// Jangan lupa import
use Livewire\Attributes\On;

new class extends Component {
  public ?int $selectedCategory = null;

  #[Computed]
  public function categories()
  {
    return Category::latest()->get();
  }

  public function select(?int $categoryId = null)
  {
    $this->selectedCategory = $categoryId;
    $this->dispatch('category-selected', categoryId: $categoryId);
  }

  // Tambahkan listener ini
  #[On('reset-category-selection')]
  public function handleResetFromSearch()
  {
    $this->selectedCategory = null;
  }
  
}; ?>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">

  {{-- Header Ringkas --}}
  <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-1">
    <x-lucide-filter class="w-3.5 h-3.5 text-indigo-500" />
    <span>Filter Kategori</span>
  </div>

  {{-- List Badge Kategori --}}
  <div class="flex flex-wrap gap-2">

    {{-- Option "Semua" --}}
    <button type="button" wire:click="select(null)" class="cursor-pointer outline-none">
      <x-ui.badge 
        :color="is_null($selectedCategory) ? 'indigo' : 'gray'" 
        size="md"
      >
        Semua
      </x-ui.badge>
    </button>

    {{-- Loop Kategori --}}
    @forelse ($this->categories as $category)
      <button 
        type="button" 
        wire:key="filter-{{ $category->id }}" 
        wire:click="select({{ $category->id }})" 
        class="cursor-pointer outline-none"
      >
        <x-ui.badge 
          :color="$selectedCategory === $category->id ? 'indigo' : 'gray'" 
          size="md"
        >
          {{ $category->name }}
        </x-ui.badge>
      </button>
    @empty
      <p class="text-xs text-slate-400 italic py-1">Belum ada kategori.</p>
    @endforelse

  </div>
</div>