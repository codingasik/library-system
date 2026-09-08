<?php

use App\Models\Member;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

new #[Layout('layouts::app')]
#[Title('Daftar Anggota')]
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
  public $editingMember = null;
  public $showModal = false;

  // Form
  public $name = '';
  public $email = '';
  public $phone = '';
  public $address = '';
  public $is_active = true;

  // Validation Rules
  protected function rules()
  {
    return [
      'name' => 'required|min:3|max:150',
      'email' => [
        'nullable',
        'email',
        Rule::unique('members', 'email')
          ->ignore($this->editingMember?->id),
      ],
      'phone' => 'nullable|min:8|max:20',
      'address' => 'nullable',
      'is_active' => 'boolean',
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

  // Computed Members
  #[Computed]
  public function members()
  {
    return Member::query()
      ->when($this->search, fn($query) => 
        $query->where(fn($q) => 
          $q->where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orWhere('phone', 'like', "%{$this->search}%")
            ->orWhere('member_code', 'like', "%{$this->search}%")
        )
      )
      ->orderBy($this->sortColumn, $this->sortDirection)
      ->paginate($this->perPage);
  }

  // Close & Reset Modal Form
  public function closeModal()
  {
    $this->showModal = false;
    $this->editingMember = null;
    $this->is_active = true;
    $this->reset([
      'name',
      'email',
      'phone',
      'address',
      'is_active',
    ]);
    $this->resetValidation();
  }

  public function openModal()
  {
    $this->closeModal();
    $this->showModal = true;
  }

  // Generate Member Code
  protected function generateMemberCode()
  {
    $number = Member::max('id') + 1;

    return 'M-' . str_pad(
      $number,
      4,
      '0',
      STR_PAD_LEFT
    );
  }

  public function edit($id)
  {
    $member = Member::findOrFail($id);

    $this->editingMember = $member;

    $this->name = $member->name;
    $this->email = $member->email;
    $this->phone = $member->phone;
    $this->address = $member->address;
    $this->is_active = $member->is_active;

    $this->resetValidation();
    $this->showModal = true;
  }

  public function save()
  {
    // Menjalankan validasi
    $validated = $this->validate();

    if ($this->editingMember) {
      $this->editingMember->update($validated);
      $message = 'Member berhasil diperbarui.';
    } else {
      $validated['member_code'] = $this->generateMemberCode();
      Member::create($validated);
      $message = 'Member berhasil ditambahkan.';
    }

    $this->closeModal();
    $this->dispatch('notify', message: $message);
  }

  public function delete($id)
  {
    $member = Member::findOrFail($id);

    if (!$member) {
      return;
    }

    if ($member->loans()->exists()) {
      $this->dispatch(
        'notify',
        message: 'Member tidak dapat dihapus karena memiliki riwayat peminjaman.', type:'error'
      );
      return;
    }

    $member->delete();
    $this->dispatch('notify', message: 'Member berhasil dihapus.');
  }
};

?>

<div class="space-y-6">

  {{-- PAGE HEADER --}}
  <x-shared.page-header
    title="Daftar Member"
    description="Kelola semua koleksi member perpustakaan.">
    <x-slot:icon>
      <x-lucide-users class="h-6 w-6" />
    </x-slot:icon>
    <x-ui.button
      variant="success"
      wire:click="openModal">
      <x-lucide-plus class="h-4 w-4" />
      Tambah Member
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
          placeholder="Cari nama, email, atau nomor anggota..."
          wire:model.live.debounce.300ms="search" />
      </div>

      {{-- Total Data --}}
      <x-ui.badge color="green">
        {{ $this->members->total() }} member
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
            Anggota
          </x-ui.table.th>

          <x-ui.table.th sortable column="email" :activeColumn="$sortColumn" :direction="$sortDirection">
            Email
          </x-ui.table.th>

          <x-ui.table.th>
            Phone
          </x-ui.table.th>

          <x-ui.table.th align="center" sortable column="is_active" :activeColumn="$sortColumn" :direction="$sortDirection">
            Status
          </x-ui.table.th>

          <x-ui.table.th align="right">
            Aksi
          </x-ui.table.th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse ($this->members as $member)
        <x-ui.table.tr wire:key="row-{{ $member->id }}">
          {{-- MEMBER --}}
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              {{-- AVATAR --}}
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 font-semibold text-indigo-600">
                {{ strtoupper(substr($member->name, 0, 1)) }}
              </div>

              <div class="min-w-0">
                <p class="truncate font-semibold text-slate-800">
                  {{ $member->name }}
                </p>

                <p class="mt-0.5 text-xs text-slate-400">
                  {{ $member->member_code }}
                </p>
              </div>
            </div>
          </td>

          {{-- EMAIL --}}
          <td class="px-5 py-4 text-slate-500">
            {{ $member->email ?: '-' }}
          </td>

          {{-- PHONE --}}
          <td class="px-5 py-4 text-slate-500">
            {{ $member->phone ?: '-' }}
          </td>

          {{-- STATUS --}}
          <td class="px-5 py-4">
            @if ($member->is_active)
              <x-ui.badge color="green">Aktif</x-ui.badge>
            @else
              <x-ui.badge color="red">Nonaktif</x-ui.badge>
            @endif
          </td>

          {{-- Aksi --}}
          <td class="px-5 py-4 text-right">
            <div class="flex justify-end gap-2">
              <x-ui.button variant="primary" wire:click="edit({{ $member->id }})" size="sm" title="Edit Member">
                <x-lucide-pencil class="h-4 w-4" />
              </x-ui.button>
              <x-ui.button variant="danger" wire:click="delete({{ $member->id }})" wire:confirm="Yakin hapus member '{{ $member->name }}' ini?" size="sm" title="Hapus Member">
                <x-lucide-trash-2 class="h-4 w-4" />
              </x-ui.button>
            </div>
          </td>
        </x-ui.table.tr>
        @empty
        <x-ui.table.empty
          icon="users"
          title="Member tidak ditemukan"
          description="Coba ubah kata kunci pencarian atau tambah member baru."
          colspan="5" />
        @endforelse
      </tbody>
    </x-ui.table>

    {{-- PAGINATION --}}
    @if ($this->members->hasPages())
    <div class="border-t border-slate-100 px-4 py-4 sm:px-5">
      {{ $this->members->links() }}
    </div>
    @endif

  </div>

  {{-- MODAL --}}
  @if ($showModal)
  <x-shared.modal
    :title="$editingMember ? 'Edit Member' : 'Tambah Member'"
    :description="$editingMember
        ? 'Perbarui informasi anggota.'
        : 'Tambahkan anggota baru ke perpustakaan.'">
    
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
          label="Nama Anggota"
          placeholder="Masukkan nama lengkap"
          wire:model="name"
          required />
        <div class="grid gap-5 sm:grid-cols-2">
          <x-ui.input
            name="email"
            label="Email"
            type="email"
            placeholder="nama@email.com"
            wire:model="email" />

          <x-ui.input
            name="phone"
            label="Nomor HP"
            placeholder="08xxxxxxxxxx"
            wire:model="phone" />
        </div>

        <x-ui.textarea
          name="address"
          label="Alamat"
          placeholder="Masukkan alamat anggota..."
          rows="4"
          wire:model="address" />

        <div>
          <p class="mb-2 text-sm font-medium text-slate-700">
            Status Anggota
          </p>
          <label
            class="flex cursor-pointer items-center gap-3">
            <input
              type="checkbox"
              wire:model="is_active"
              class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span
              class="text-sm text-slate-600">
              Anggota aktif
            </span>
          </label>
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
          wire:target="save">
          <span wire:loading.remove wire:target="save">
            {{ $editingMember
              ? 'Perbarui Member'
              : 'Simpan Member'
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