<?php

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

new #[Layout('layouts::app')]
#[Title('Daftar Kategori')]
class extends Component
{
  use WithPagination;

  #[Url]
  public $perPage = 10;

  #[Url(except: '')]
  public $search = '';

  public $sortColumn = 'name';
  public $sortDirection = 'asc';

  // Modal State
  public $editingCategory = null;
  public $showModal = false;

  // Form
  public $name = '';
  public $description = '';

  // Validation Rules
  protected function rules()
  {
    return [
      'name' => [
        'required',
        'min:3',
        'max:80',
        Rule::unique('categories', 'name')
          ->ignore($this->editingCategory?->id),
      ],

      'description' => 'nullable',
    ];
  }

  public function updatedPerPage()
  {
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

  // Computed categories
  #[Computed]
  public function categories()
  {
    return Category::query()
      ->when($this->search, fn($query) => 
        $query->where(fn($q) => 
          $q->where('name', 'like', "%{$this->search}%")
        )
      )
      ->orderBy($this->sortColumn, $this->sortDirection)
      ->paginate($this->perPage);
  }

  // Close & Reset Modal Form
  public function closeModal()
  {
    $this->showModal = false;
    $this->editingCategory = null;

    $this->reset([
      'name',
      'description',
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
    $category = Category::findOrFail($id);

    $this->editingCategory = $category;

    $this->name = $category->name;
    $this->description = $category->description;

    $this->resetValidation();
    $this->showModal = true;
  }

  public function save()
  {
    // Menjalankan validasi
    $validated = $this->validate();

    if ($this->editingCategory) {
      $this->editingCategory->update($validated);
      $message = 'Category berhasil diperbarui.';
    } else {
      Category::create($validated);
      $message = 'Category berhasil ditambahkan.';
    }

    $this->closeModal();
    $this->dispatch('notify', message: $message);
  }

  public function delete($id)
  {
    $category = Category::find($id);

    if (!$category) {
      return;
    }

    if ($category->books()->exists()) {
      $this->dispatch(
        'notify',
        message: 'Category tidak dapat dihapus karena digunakan di data Books',
        type:'error'
      );
      return;
    }

    $category->delete();
    $this->dispatch('notify', message: 'Category berhasil dihapus.');
  }
};

?>

<div class="space-y-6">

  {{-- PAGE HEADER --}}
  <x-shared.page-header
    title="Daftar Category"
    description="Kelola semua koleksi category perpustakaan.">
    <x-slot:icon>
      <x-lucide-tags class="h-6 w-6" />
    </x-slot:icon>
    <x-ui.button
      variant="success"
      wire:click="openModal">
      <x-lucide-plus class="h-4 w-4" />
      Tambah Category
    </x-ui.button>
  </x-shared.page-header>

  {{-- FILTER & TABLE --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

  {{-- SEARCH SECTION --}}
  <div class="border-b border-slate-100 bg-slate-50/50 p-4 sm:p-5">
    <div class="flex flex-col gap-3 md:flex-row md:items-center">

      {{-- Search Input --}}
      <div class="flex-1">
        <x-ui.input
          name="search"
          icon="search"
          placeholder="Cari kategori..."
          wire:model.live.debounce.300ms="search" />
      </div>

      {{-- Total Data --}}
      <x-ui.badge color="green">
        {{ $this->categories->total() }} category
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
          <x-ui.table.th sortable column="name" :activeColumn="$sortColumn" :direction="$sortDirection">
            Kategori
          </x-ui.table.th>
          
          <x-ui.table.th>
            Deskripsi
          </x-ui.table.th>

          <x-ui.table.th align="center" sortable column="created_at" :activeColumn="$sortColumn" :direction="$sortDirection">
            Dibuat
          </x-ui.table.th>

          <x-ui.table.th align="right">
            Aksi
          </x-ui.table.th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse ($this->categories as $category)

        <x-ui.table.tr wire:key="row-{{ $category->id }}">
          {{-- CATEGORY --}}
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-lg">
                <x-lucide-tags class="h-4 w-4" />
              </div>
              <div class="min-w-0">
                <p class="truncate font-semibold text-slate-800">
                  {{ $category->name }}
                </p>
                <p class="mt-0.5 text-xs text-slate-400">
                  ID #{{ $category->id }}
                </p>
              </div>
            </div>
          </td>

          {{-- DESCRIPTION --}}
          <td class="max-w-md px-5 py-4 text-slate-500">
            @if ($category->description)
              <p class="line-clamp-2">
                {{ $category->description }}
              </p>
            @else
              <span class="text-xs text-slate-400">
                Tidak ada deskripsi
              </span>
            @endif
          </td>

          {{-- CREATED AT --}}
          <td class="px-5 py-4 text-slate-500">
            {{ $category->created_at->diffForHumans() }}
          </td>

          {{-- Aksi --}}
          <td class="px-5 py-4 text-right">
            <div class="flex justify-end gap-2">
              <x-ui.button variant="primary" wire:click="edit({{ $category->id }})" size="sm" title="Edit Category">
                <x-lucide-pencil class="h-4 w-4" />
              </x-ui.button>
              <x-ui.button variant="danger" wire:click="delete({{ $category->id }})" wire:confirm="Yakin hapus category '{{ $category->name }}' ini?" size="sm" title="Hapus Category">
                <x-lucide-trash-2 class="h-4 w-4" />
              </x-ui.button>
            </div>
          </td>
        </x-ui.table.tr>
        @empty
        <x-ui.table.empty
          icon="tags"
          title="Category tidak ditemukan"
          description="Coba ubah kata kunci pencarian atau tambah category baru."
          colspan="5" />
        @endforelse
      </tbody>
    </x-ui.table>

    {{-- PAGINATION --}}
    @if ($this->categories->hasPages())
    <div class="border-t border-slate-100 px-4 py-4 sm:px-5">
      {{ $this->categories->links() }}
    </div>
    @endif

  </div>

  {{-- MODAL --}}
  @if ($showModal)
  <x-shared.modal
    :title="$editingCategory ? 'Edit Category' : 'Tambah Category'"
    :description="$editingCategory
        ? 'Perbarui informasi category.'
        : 'Tambahkan category baru ke perpustakaan.'">
    
    <x-slot:close>
      <button
        type="button"
        wire:click="closeModal"
        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <x-lucide-x class="h-4 w-4" />
      </button>
    </x-slot:close>

    <form wire:submit="save" class="flex min-h-0 flex-1 flex-col">
      <div class="flex-1 space-y-5 overflow-y-auto px-6 py-6">

        <x-ui.input
          name="name"
          label="Nama Kategori"
          placeholder="Contoh: Pemrograman"
          wire:model="name"
          required />
        <x-ui.textarea
          name="description"
          label="Deskripsi"
          placeholder="Tulis deskripsi kategori..."
          rows="5"
          wire:model="description" />

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
          <span wire:loading.remove wire:target="save">
            {{ $editingCategory
              ? 'Perbarui Category'
              : 'Simpan Category'
            }}
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