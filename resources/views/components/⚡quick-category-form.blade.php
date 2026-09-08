<?php
// resources/views/components/⚡quick-category-form.blade.php

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\Computed;

new class extends Component {
  public $name = '';
  //public $successMessage = null;

  // Tambahkan method ini
  public function updatedName($value)
  {
    $this->name = ucfirst($value);
  }

  // Tambahkan juga Computed
  #[Computed]
  public function categories()
  {
    return Category::latest()->get();
  }

  public function save()
  {
    // Tammbahkan validasi tidak boleh kosong
    $nameTrim = trim($this->name);
    if ( $nameTrim === '') {
      return $this->dispatch('notify', message: 'Kategori tidak boleh kosong',type:'error');
    }
    // Tammbahkan validasi kategori sudah ada
    $exists = Category::where('name', $nameTrim)->exists();
    if($exists){
      return $this->dispatch('notify', message: 'Kategori sudah ada',type:'error');
    }

    Category::create(['name' => $this->name]);

    //$this->successMessage = "Kategori \"{$this->name}\" berhasil ditambahkan.";

    $this->dispatch('notify', message: "Kategori \"{$this->name}\" berhasil ditambahkan.")->to('toast');

    $this->reset('name');
  }
}; ?>

<div class="p-6 mx-auto max-w-md bg-white rounded-2xl shadow">
  {{-- Header Komponen --}}
  <div class="flex items-center gap-2 mb-4 text-gray-800">
    <x-lucide-folder-plus class="w-5 h-5 text-indigo-600" />
    <h3 class="text-base font-semibold">Kelola Kategori</h3>
  </div>
  {{-- 1. Form Tambah Kategori --}}
  <form wire:submit="save" class="flex gap-2">

    <div class="flex-1">
      <x-ui.input 
        name="name" 
        placeholder="Nama kategori baru..." 
        icon="tag"
        wire:model="name"
      />
    </div>

    <x-ui.button 
      type="submit" 
      variant="primary" 
      size="md"
      wire:loading.attr="disabled" 
      wire:target="save"
    >
      {{-- State Normal: Icon Plus --}}
      <x-lucide-plus wire:loading.remove wire:target="save" class="w-4 h-4" />

      {{-- State Loading: Icon Spinner Berputar --}}
      <x-lucide-loader-2 wire:loading wire:target="save" class="w-4 h-4 animate-spin" />

      {{-- Teks Tombol --}}
      <span wire:loading.remove wire:target="save">Tambah</span>
      <span wire:loading wire:target="save">Menyimpan...</span>
    </x-ui.button>
    
  </form>

  {{-- Pesan Sukses --}}
  {{--
  @if ($successMessage)
  <p class="flex items-center gap-1.5 mt-2.5 text-xs font-medium text-emerald-600">
    <x-lucide-check-circle class="w-3.5 h-3.5" />
    <span>{{ $successMessage }}</span>
  </p>
  @endif
  --}}

  {{-- Divider / Garis Pemisah --}}
  <div class="my-5 border-t border-gray-100"></div>

  {{-- 2. Tampilan List Kategori yang Ada --}}
  <div>
    <div class="flex justify-between items-center mb-3">
      <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Daftar Kategori</span>
      <span class="px-2 py-0.5 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-full">
        {{-- Sudah pakai computed, jadi ubah tanpa () --}}
        {{ $this->categories->count() }}
      </span>
    </div>

    {{-- Sudah pakai computed, jadi ubah tanpa () --}}
    @php $categoryList = $this->categories; @endphp

    @if ($categoryList->isNotEmpty())
      <ul class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
        @foreach ($categoryList as $category)
          <li wire:key="category-{{ $category->id }}" class="flex items-center justify-between p-2 rounded-lg bg-gray-50 hover:bg-gray-100/80 transition">
            <div class="flex items-center gap-2 min-w-0">
              <x-lucide-folder class="w-4 h-4 text-indigo-500 shrink-0" />
              <span class="text-sm font-medium text-gray-700 truncate">{{ $category->name }}</span>
            </div>
          </li>
        @endforeach
      </ul>
    @else
      {{-- Empty State jika data masih kosong --}}
      <p class="py-3 text-center text-xs text-gray-400">Belum ada kategori yang dibuat.</p>
    @endif
  </div>
</div>