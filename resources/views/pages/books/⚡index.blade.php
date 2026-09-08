<?php

use App\Models\Book;
use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;

new #[Layout('layouts::app')]
#[Title('Daftar Buku')]
class extends Component
{
  use WithPagination;
  use WithFileUploads;

  #[Url(except: '')]
  public $search = '';
  public $categoryId = null;
  public $sortColumn = 'title';
  public $sortDirection = 'asc';

  // Modal State
  public $editingBook = null;
  public $showModal = false;

  public $title = '';
  public $author = '';
  public $isbn = '';
  public $publisher = '';
  public $year = '';
  public $description = '';
  public $stock = 0;
  public $cover;
  public $category_id = null;

  protected function rules()
  {
    return [
      'title' => 'required|min:3|max:255',
      'author' => 'required|min:5|max:255',

      'isbn' => [
        'required',
        'min:5',
        'max:20',
        Rule::unique('books', 'isbn')
            ->ignore($this->editingBook?->id),
      ],

      'publisher' => 'nullable|max:255',
      'year' => 'nullable|integer|min:1900|max:2100',
      'description' => 'nullable',
      'stock' => 'required|integer|min:0',
      'cover' => 'nullable|image|max:1024',
      'category_id' => 'nullable|exists:categories,id',
    ];
  }


  #[Url]
  public $perPage = 10;

  public function updatedPerPage()
  {
    $this->resetPage();
  }

  // Filter Category
  #[On('category-selected')]
  public function applyCategoryFilter($categoryId)
  {
    $this->categoryId = $categoryId;
    $this->resetPage();
  }

  public function updatedSearch()
  {
    $this->resetPage();
  }

  // Sorting
  public function sortBy($column)
  {
    if ($this->sortColumn === $column) {
      $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      $this->sortColumn = $column;
      $this->sortDirection = 'asc';
    }
  }

  // Computed Books
  #[Computed]
  public function books()
  {
    $search = $this->search;

    return Book::query()
      ->with('category')
      ->when($search, function ($q) use ($search) {
        $q->where(function ($query) use ($search) {
          $query->where('title', 'like', "%{$search}%")
            ->orWhere('author', 'like', "%{$search}%");
        });
      })
      ->when($this->categoryId, fn($q) => $q->where('category_id', $this->categoryId))
      ->orderBy($this->sortColumn, $this->sortDirection)
      ->paginate($this->perPage);
  }

  // Computed Categories
  #[Computed]
  public function categories()
  {
    return Category::all();
  }

  // Close & Reset Modal Form
  public function closeModal()
  {
    $this->showModal = false;
    $this->editingBook = null;
    $this->cover = null;
    $this->reset([
      'title',
      'author',
      'isbn',
      'publisher',
      'description',
      'year',
      'stock',
      'category_id'
    ]);
    $this->resetValidation();
  }

  public function openModal()
  {
    $this->closeModal();
    $this->showModal = true;
  }

  public function edit($id)
  {
    $book = Book::findOrFail($id);

    $this->editingBook = $book;
    $this->title = $book->title;
    $this->author = $book->author;
    $this->isbn = $book->isbn;
    $this->publisher = $book->publisher;
    $this->year = $book->year;
    $this->description = $book->description;
    $this->stock = $book->stock;
    $this->category_id = $book->category_id;
    $this->cover = null;

    $this->resetValidation();
    $this->showModal = true;
  }

  public function save()
  {
    // Menjalankan validasi
    $validated = $this->validate();

    $coverPath = $this->editingBook?->cover;

    if ($this->cover) {
      $coverPath = $this->cover->store('books', 'public');

      if ($this->editingBook?->cover) {
        Storage::disk('public')->delete($this->editingBook->cover);
      }
    }

    // Tambahkan ke $validated
    $validated['cover'] = $coverPath;

    if ($this->editingBook) {
      // cukup gunakan data dari $validated langsung
      $this->editingBook->update($validated);
      $message = 'Buku berhasil diperbarui.';
    } else {
      Book::create($validated);
      $message = 'Buku berhasil ditambahkan.';
    }

    $this->closeModal();
    $this->dispatch('notify', message: $message);
  }

  public function delete($id)
  {
    $book = Book::find($id);

    if (!$book) {
      return;
    }

    if ($book->loans()->exists()) {
      $this->dispatch(
        'notify',
        message: 'Buku tidak dapat dihapus karena memiliki riwayat peminjaman.',
        tipe:'error'
      );
      return;
    }

    if ($book->cover) {
      Storage::disk('public')->delete($book->cover);
    }

    $book->delete();
    $this->dispatch('notify', message: 'Buku berhasil dihapus.');
  }
};

?>

<div class="space-y-6">

  {{-- PAGE HEADER --}}
  <x-shared.page-header
    title="Daftar Buku"
    description="Kelola semua koleksi buku perpustakaan.">
    <x-slot:icon>
      <x-lucide-book class="h-6 w-6" />
    </x-slot:icon>
    <x-ui.button
      variant="success"
      wire:click="openModal">
      <x-lucide-plus class="h-4 w-4" />
      Tambah Buku
    </x-ui.button>
  </x-shared.page-header>

  {{-- FILTER & TABLE --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

{{-- SEARCH SECTION --}}
<div class="border-b border-slate-100 bg-slate-50/50 p-4 sm:p-5">
  <div class="border-b border-slate-100 mb-4 pb-4">
    <livewire:category-filter />
  </div>
  <div class="flex flex-col gap-3 md:flex-row md:items-center">

    {{-- Search Input --}}
    <div class="flex-1">
      <x-ui.input
        name="search"
        icon="search"
        placeholder="Cari judul atau penulis..."
        wire:model.live.debounce.300ms="search" />
    </div>

    {{-- Total Data --}}
    <x-ui.badge color="green">
      {{ $this->books->total() }} buku
    </x-ui.badge>

    {{-- Dropdown perPage --}}
    <x-ui.select
      wire:model.live="perPage"
      name="perpage"
    >
      <option value="10">10 / halaman</option>
      <option value="25">25 / halaman</option>
      <option value="50">50 / halaman</option>
    </x-ui.select>

  </div>
</div>

    {{-- TABLE --}}
    <x-ui.table>
      <thead class="border-b border-slate-200 bg-slate-50">
        <tr>
          <x-ui.table.th sortable column="title" :activeColumn="$sortColumn" :direction="$sortDirection">
            Buku
          </x-ui.table.th>

          <x-ui.table.th sortable column="author" :activeColumn="$sortColumn" :direction="$sortDirection">
            Penulis
          </x-ui.table.th>

          <x-ui.table.th>
            Kategori
          </x-ui.table.th>

          <x-ui.table.th align="center" sortable column="stock" :activeColumn="$sortColumn" :direction="$sortDirection">
            Stok
          </x-ui.table.th>

          <x-ui.table.th align="right">
            Aksi
          </x-ui.table.th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse ($this->books as $book)
        <x-ui.table.tr wire:key="row-{{ $book->id }}">
          {{-- Data Buku --}}
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-indigo-50 text-indigo-600">
                @if ($book->cover)
                  <img src="{{ asset('storage/' . $book->cover) }}" class="h-full w-full object-cover" />
                @else
                  <x-lucide-book class="h-5 w-5" />
                @endif
              </div>
              <div class="min-w-0">
                <p class="truncate font-semibold text-slate-800">{{ $book->title }}</p>
                <p class="mt-0.5 text-xs text-slate-400">ID #{{ $book->id }}</p>
              </div>
            </div>
          </td>

          {{-- Penulis --}}
          <td class="px-5 py-4 text-slate-500">{{ $book->author ?: '-' }}</td>

          {{-- Kategori --}}
          <td class="px-5 py-4">
            @if ($book->category)
              <x-ui.badge color="purple">{{ $book->category->name }}</x-ui.badge>
            @else
              <span class="text-xs text-slate-400">Tanpa kategori</span>
            @endif
          </td>

          {{-- Stok --}}
          <td class="px-5 py-4 text-center">
            @if ($book->stock == 0)
              <x-ui.badge color="red">Habis</x-ui.badge>
            @elseif ($book->stock <= 3)
              <x-ui.badge color="yellow">{{ $book->stock }} tersisa</x-ui.badge>
            @else
              <x-ui.badge color="green">{{ $book->stock }}</x-ui.badge>
            @endif
          </td>

          {{-- Aksi --}}
          <td class="px-5 py-4 text-right">
            <div class="flex justify-end gap-2">
              {{-- Detail --}}
              <x-ui.button as="a" href="{{ route('books.show', $book->id) }}" variant="secondary" size="sm" title="Lihat Detail">
                <x-lucide-eye class="h-4 w-4" />
              </x-ui.button>
              <x-ui.button variant="primary" wire:click="edit({{ $book->id }})" size="sm" title="Edit Book">
                <x-lucide-pencil class="h-4 w-4" />
              </x-ui.button>
              <x-ui.button variant="danger" wire:click="delete({{ $book->id }})" wire:confirm="Yakin hapus buku '{{ $book->title }}' ini?" size="sm" title="Hapus Book">
                <x-lucide-trash-2 class="h-4 w-4" />
              </x-ui.button>
            </div>
          </td>
        </x-ui.table.tr>
        @empty
        <x-ui.table.empty
          icon="book"
          title="Buku tidak ditemukan"
          description="Coba ubah kata kunci pencarian atau tambah buku baru."
          colspan="5" />
        @endforelse
      </tbody>
    </x-ui.table>

    {{-- PAGINATION --}}
    @if ($this->books->hasPages())
    <div class="border-t border-slate-100 px-4 py-4 sm:px-5">
      {{ $this->books->links() }}
    </div>
    @endif

  </div>

  {{-- MODAL --}}
  @if ($showModal)
  <x-shared.modal
    :title="$editingBook ? 'Edit Buku' : 'Tambah Buku'"
    :description="$editingBook
        ? 'Perbarui informasi buku.'
        : 'Tambahkan buku baru ke koleksi.'">
    
    <x-slot:close>
      <button
        type="button"
        wire:click="closeModal"
        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        ✕
      </button>
    </x-slot:close>

    <form wire:submit="save" class="flex min-h-0 flex-1 flex-col">
      <div class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
        <div class="grid gap-5 sm:grid-cols-2">
          <x-ui.input
            name="title"
            label="Judul Buku"
            placeholder="Contoh: Belajar Laravel"
            wire:model="title"
            required />

          <x-ui.input
            name="author"
            label="Penulis"
            placeholder="Nama penulis"
            wire:model="author"
            required />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
          <x-ui.input
            name="isbn"
            label="ISBN"
            placeholder="Tulis No ISBN"
            wire:model="isbn"
            required />

          <x-ui.input
            name="publisher"
            label="Publisher"
            placeholder="Nama Publisher"
            wire:model="publisher" />
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
          <x-ui.select
            name="category_id"
            label="Kategori"
            placeholder="Pilih kategori"
            wire:model="category_id">
            @foreach ($this->categories as $category)
            <option value="{{ $category->id }}">
              {{ $category->name }}
            </option>
            @endforeach
          </x-ui.select>

          <x-ui.input
            name="year"
            label="Year"
            type="number"
            placeholder="Exp : 2026"
            wire:model="year" />
        </div>

        <x-ui.textarea
          name="description"
          label="Deskripsi"
          placeholder="Tulis deskripsi buku..."
          rows="4"
          wire:model="description" />

        <div class="grid gap-5 sm:grid-cols-2">
          <x-ui.input
            name="stock"
            label="Stock"
            type="number"
            min="0"
            wire:model="stock" />

          <div class="space-y-2">
            <x-ui.input
              name="cover"
              label="Cover Buku"
              type="file"
              wire:model="cover"
              accept="image/*"
              hint="Format: JPG, PNG (Maksimal 2 MB)" />

            <div class="flex items-center gap-4 mt-2">
              @if ($cover)
              <div>
                <p class="text-xs font-medium text-emerald-600 mb-1">Preview Cover Baru:</p>
                <img src="{{ $cover->temporaryUrl() }}" class="h-20 w-16 object-cover rounded-lg border border-slate-200 shadow-sm" />
              </div>
              @elseif ($editingBook?->cover)
              <div>
                <p class="text-xs font-medium text-slate-500 mb-1">Cover Saat Ini:</p>
                <img src="{{ asset('storage/' . $editingBook->cover) }}" class="h-20 w-16 object-cover rounded-lg border border-slate-200 shadow-sm" />
              </div>
              @endif
            </div>
          </div>
        </div>

      </div>

      {{-- Modal Footer --}}
      <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
        <x-ui.button
          type="button"
          wire:click="closeModal"
          variant="secondary">
          Batal
        </x-ui.button>

        <x-ui.button
          type="submit"
          variant="primary"
          wire:loading.attr="disabled"
          wire:target="save, cover">
          <span wire:loading.remove wire:target="save, cover">
            Simpan Buku
          </span>
          <span wire:loading wire:target="cover">
            Mengunggah...
          </span>
          <span wire:loading wire:target="save">
            Menyimpan...
          </span>
        </x-ui.button>
      </div>
    </form>

  </x-shared.modal>
  @endif

</div>