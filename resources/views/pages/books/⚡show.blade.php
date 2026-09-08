<?php
// resources/views/pages/books/⚡show.blade.php

use App\Models\Book;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')]
class extends Component {
  public Book $book;
  public string $title;

  public function mount(Book $book){
    $this->book = $book;
    $this->title = $book->title;
  }

  public function render()
  {
    return $this->view()
      ->title("Detail: {$this->title}");
  }

}; ?>

<div class="max-w-4xl mx-auto space-y-6">

  {{-- Top Action / Header Navigation --}}
  <div class="flex items-center justify-between">
    <a href="{{ route('books.index') }}" wire:navigate 
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
      <x-lucide-arrow-left class="w-4 h-4" />
      <span>Kembali ke Daftar Buku</span>
    </a>

    {{-- Badge Status Stok --}}
    @if($book->stock > 0)
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        Tersedia ({{ $book->stock }} Eksemplar)
      </span>
    @else
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
        Stok Habis
      </span>
    @endif
  </div>

  {{-- Card Utama Detail Buku --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
      
      {{-- Kolom Kiri: Cover Gambar / Placeholder Icon --}}
      <div class="flex flex-col items-center justify-center overflow-hidden rounded-xl bg-slate-900 border border-slate-800 shadow-inner min-h-[260px]">
        @if ($book->cover)
          <img 
            src="{{ asset('storage/' . $book->cover) }}" 
            alt="{{ $book->title }}" 
            class="h-full w-full object-cover max-h-[380px]"
          />
        @else
          <div class="flex flex-col items-center justify-center p-8 text-white text-center">
            <div class="p-4 bg-slate-800 rounded-2xl border border-slate-700/60 mb-4 shadow-inner">
              <x-lucide-book-open class="w-12 h-12 text-indigo-400" />
            </div>
            <span class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Tanpa Cover</span>
          </div>
        @endif
      </div>

      {{-- Kolom Kanan: Detail Informasi --}}
      <div class="md:col-span-2 space-y-6 flex flex-col justify-between">
        <div class="space-y-4">
          
          {{-- Judul Buku --}}
          <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
              {{ $book->title }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Ditulis oleh <span class="font-medium text-gray-700">{{ $book->author ?: '-' }}</span>
            </p>
          </div>

          {{-- Information Grid --}}
          <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-100">
            <div class="space-y-1">
              <span class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Kategori</span>
              <p class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                <x-lucide-tag class="w-4 h-4 text-indigo-500" />
                {{ $book->category->name ?? 'Tanpa Kategori' }}
              </p>
            </div>

            <div class="space-y-1">
              <span class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Jumlah Stok</span>
              <p class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                <x-lucide-layers class="w-4 h-4 text-indigo-500" />
                {{ $book->stock }} Buku
              </p>
            </div>

            @if($book->isbn)
              <div class="space-y-1">
                <span class="text-xs text-gray-400 uppercase font-semibold tracking-wider">ISBN</span>
                <p class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                  <x-lucide-barcode class="w-4 h-4 text-indigo-500" />
                  {{ $book->isbn }}
                </p>
              </div>
            @endif

            @if($book->publisher)
              <div class="space-y-1">
                <span class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Penerbit</span>
                <p class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                  <x-lucide-building-2 class="w-4 h-4 text-indigo-500" />
                  {{ $book->publisher }} {{ $book->year ? "({$book->year})" : '' }}
                </p>
              </div>
            @endif
          </div>

          {{-- Deskripsi --}}
          @if($book->description)
            <div class="space-y-1.5">
              <span class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Deskripsi</span>
              <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                {{ $book->description }}
              </p>
            </div>
          @endif

        </div>

        

      </div>

    </div>
  </div>

</div>