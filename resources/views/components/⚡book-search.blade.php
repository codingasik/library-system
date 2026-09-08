<?php
// resources/views/components/⚡book-search.blade.php

use Livewire\Component;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
// Jangan lupa import ini
use Livewire\Attributes\On;

new class extends Component {
  #[Url(as:'q')]
  public string $search = '';
  public $searchCount = 0;
  // Tambahkan ini
  public $categoryId = null;

  //Tambahkan ini
  #[On('category-selected')]
  public function applyCategoryFilter($categoryId)
  {
    $this->categoryId = $categoryId;
  }

  public function updatedSearch($value)
  {
    if ($value !== '') {
      $this->searchCount++;
    }
  }

  public function resetAll()
  {
    $this->reset(['search', 'categoryId']);
    
    // Dispatch event balik ke komponen filter kategori
    $this->dispatch('reset-category-selection');
  }

  #[Computed]
  public function books(): Collection
  {
    if (strlen($this->search) < 2 && !$this->categoryId) {
      return collect();
    }
    
    return Book::query()
      ->with('category')
      // Filter Kategori (hanya jika categoryId terisi)
      ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId))
      // Filter Kata Kunci (hanya jika $search diisi minimal 2 karakter)
      ->when(strlen($this->search) >= 2, function (Builder $query) {
        $query->where(function (Builder $q) {
          $q->where('title', 'like', "%{$this->search}%")
            ->orWhere('author', 'like', "%{$this->search}%")
            ->orWhereHas('category', function (Builder $catQuery) {
              $catQuery->where('name', 'like', "%{$this->search}%");
            });
        });
      })
      ->limit(5)
      ->get();
  }
  
}; ?>

<div class="p-6 mx-auto bg-white rounded-2xl border border-gray-100 shadow">
  {{-- Total pencarian  --}}
  <div class="flex justify-between items-center mb-2 px-1 text-xs text-gray-400">
    <span class="flex items-center gap-1">
      <x-lucide-activity class="w-3.5 h-3.5 text-indigo-500" />
      <span>Total Pencarian:</span>
    </span>
    <span class="px-2 py-0.5 font-bold text-indigo-600 bg-indigo-50 rounded-full border border-indigo-100/50">
      {{ $searchCount }}x
    </span>
  </div>
  {{-- Input Search --}}
  <div class="mb-2">
    <x-ui.input 
      name="search" 
      placeholder="Cari judul, penulis buku atau category..." 
      icon="search"
      wire:model.live.debounce.300ms="search"
    />
  </div>

  {{-- Hasil Pencarian --}}
  @php $results = $this->books; @endphp
  
  @if ($results->isNotEmpty())

  {{-- Tombol Reset + Total Pencarian --}}
  <div class="flex justify-between">
    {{-- Tombol Reset --}}
    @if ($search !== '' || $categoryId)
      <button 
        type="button" 
        wire:click="resetAll"
        class="text-xs text-rose-500 hover:text-rose-600 font-medium flex items-center gap-1 cursor-pointer"
      >
        <x-lucide-rotate-ccw class="w-3 h-3" />
        <span>Reset Semua Filter</span>
      </button>
    @endif
    {{-- Total pencarian --}}
    <p class="text-xs text-gray-400 mb-2">Ditemukan {{ $results->count() }} buku</p>
  </div>

  {{-- List data books --}}
  <ul class="space-y-2">
    @foreach ($results as $book)
    {{-- Tambah wir:key setiap kali looping --}}
    <li wire:key="book-{{ $book->id }}" class="flex gap-3 items-center p-2.5 rounded-lg border border-gray-100 hover:bg-indigo-50 transition">
      <div class="p-2 text-indigo-600 bg-indigo-50 rounded-lg shrink-0">
        <x-lucide-book-open class="w-4 h-4" />
      </div>
      <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-gray-800 truncate">{{ $book->title }}</p>
        <p class="text-xs text-gray-400 truncate">{{ $book->author }}</p>
        <p class="text-xs text-gray-400 truncate">{{ $book->category->name }}</p>
      </div>
    </li>
    @endforeach
  </ul>
  
  {{-- Jika data kosong --}}
  @elseif (strlen($search) >= 2 || $categoryId)
  <div class="py-4 text-center text-gray-400">
    <x-lucide-frown class="mx-auto mb-1 w-6 h-6" />
    <p class="text-xs">Buku tidak ditemukan.</p>
  </div>
  @endif
  
</div>