<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app')]
#[Title('Daftar Peminjaman')]
class extends Component
{
  use WithPagination;

  #[Url]
  public $perPage = 10;

  #[Url(except: '')]
  public $search = '';

  #[Url(except: '')]
  public $statusFilter = '';
  public $sortColumn = 'loan_date';
  public $sortDirection = 'desc';

  public $showModal = false;

  // Form
  public $book_id = null;
  public $member_id = null;
  public $loan_date = '';
  public $due_date = '';
  public $return_date = null;
  public $status = 'borrowed';
  public $notes = '';

  // Validation Rules
  protected function rules()
  {
    return [
      'book_id' => ['required', 'exists:books,id'],
      'member_id' => ['required', 'exists:members,id'],
      'loan_date' => ['required', 'date'],
      'due_date' => ['required', 'date', 'after_or_equal:loan_date'],
      'notes' => ['nullable', 'string', 'max:1000'],
    ];
  }

  public function updatedPerPage()
  {
    $this->resetPage();
  }

  public function updatedStatusFilter()
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

  // Computed Loans
  #[Computed]
  public function loans()
  {
    return Loan::query()
      ->with(['book', 'member'])
      ->when($this->search, fn($query) => 
        $query->where(fn($query) => 
          $query->whereHas('book', fn($query)=>
                  $query->where('title', 'like', "%{$this->search}%"))
                ->orWhereHas('member', fn($query)=>
                  $query->where('name', 'like', "%{$this->search}%")))
      )
      ->when($this->statusFilter === 'overdue', fn($query)=>
              $query->where('status', 'borrowed')
                    ->whereDate('due_date','<',today()))
      ->when($this->statusFilter === 'borrowed', fn($query)=>
              $query->where('status', 'borrowed')
                    ->whereDate('due_date','>=',today()))
      ->when($this->statusFilter === 'returned', fn($query)=>
              $query->where('status', 'returned'))
      ->orderBy($this->sortColumn, $this->sortDirection)
      ->paginate($this->perPage);
  }

  #[Computed]
  public function books()
  {
    return Book::query()
      ->where('stock', '>', 0)
      ->orderBy('title')
      ->get();
  }

  #[Computed]
  public function members()
  {
    return Member::query()
      ->where('is_active', true)
      ->orderBy('name')
      ->get();
  }

  // Close & Reset Modal Form
  public function closeModal()
  {
    $this->showModal = false;

    $this->loan_date = now()->format('Y-m-d');

    $this->due_date = now()
      ->addDays(7)
      ->format('Y-m-d');

    $this->reset([
      'book_id',
      'member_id',
      'return_date',
      'notes',
    ]);

    $this->resetValidation();
  }

  public function openModal()
  {
    $this->closeModal();
    $this->showModal = true;
  }

  public function save()
  {
    // Menjalankan validasi
    $validated = $this->validate();

    // Cek member apakah aktif
    $member = Member::findOrFail($this->member_id);

    if (!$member->is_active) {
      $this->dispatch(
        'notify-error',
        message: 'Member sudah tidak aktif dan tidak dapat meminjam buku.'
      );
      return;
    }
    
    // Cek apakah member masih meminjam buku yang sama
    $alreadyBorrowed = Loan::query()
      ->where('book_id', $validated['book_id'])
      ->where('member_id', $validated['member_id'])
      ->where('status', 'borrowed')
      ->exists();

    if ($alreadyBorrowed) {
      $this->dispatch(
        'notify',
        message: 'Member masih meminjam buku ini.', type:'error'
      );
      return;
    }

    // Ambil data buku
    $book = Book::findOrFail( $validated['book_id'] );

    // Pastikan stok masih tersedia
    if ($book->stock <= 0) {
      $this->dispatch(
        'notify',
        message: 'Buku sudah tidak tersedia.', type:'error'
      );
      return;
    }
    
    // Lakukan transaction mencegah data aman
    DB::transaction(function () use ($validated, $book) {
      Loan::create([
        ...$validated,
        'status' => 'borrowed'
      ]);
      $book->decrement('stock');
    });

    $this->closeModal();
    return $this->dispatch('notify', message: 'Peminjaman berhasil ditambahkan.');
  }

  public function returnBook($id){
    $loan = Loan::findOrFail($id);

    // memastikan data belum dikembalikan
    if($loan->status == 'returned'){
      return $this->dispatch(
        'notify',
        message : 'Buku sudah dikembalikan sebelumnya.', type: 'error'
      );
    }

    DB::transaction(function () use ($loan) {
      $loan->update([
        'return_date' => now(),
        'status' => 'returned'
      ]);
      $loan->book->increment('stock');
    });

    return $this->dispatch(
      'notify',
      message : 'Buku berhasil dikembalikan'
    );
  }

  // Perpanjangan peminjaman
  public function extend($id)
  {
    $loan = Loan::findOrFail($id);

    // Pastikan peminjaman belum dikembalikan
    if ($loan->status === 'returned') {
      return $this->dispatch(
        'notify',
        message: 'Peminjaman yang sudah dikembalikan tidak dapat diperpanjang.', 
        type: 'error'
      );
    }

    // Pastikan peminjaman belum terlambat (menggunakan display_status)
    if ($loan->display_status === 'overdue') {
      return $this->dispatch(
        'notify',
        message: 'Peminjaman yang sudah terlambat tidak dapat diperpanjang.', 
        type: 'error'
      );
    }

    $loan->update([
      'due_date' => $loan->due_date->copy()->addDays(7),
    ]);

    return $this->dispatch(
        'notify',
        message: 'Peminjaman berhasil diperpanjang 7 hari.'
    );
  }

  public function delete($id)
  {
    $loan = Loan::findOrFail($id);

    // memastikan sudah ada riwayat peminjaman atau belum
    if($loan->status == 'returned'){
      return $this->dispatch(
        'notify',
        message : 'Data peminjaman yang sudah dikembalikan tidak dapat dihapus.', type: 'error'
      );
    }

    // Data yang sudah terlambat tidak boleh dihapus
    if ($loan->display_status === 'overdue') {
      return $this->dispatch(
        'notify',
        message: 'Peminjaman yang sudah terlambat tidak dapat dihapus.', type:'error'
      );
    }

    DB::transaction(function () use ($loan){
      $loan->book->increment('stock');
      $loan->delete();
    });

    return $this->dispatch('notify', message: 'Data peminjaman berhasil dihapus.');
  }
};

?>

<div class="space-y-6">

  {{-- PAGE HEADER --}}
  <x-shared.page-header
    title="Daftar Loan"
    description="Kelola semua koleksi loan perpustakaan.">
    <x-slot:icon>
      <x-lucide-repeat class="h-6 w-6" />
    </x-slot:icon>
    <x-ui.button
      variant="success"
      wire:click="openModal">
      <x-lucide-plus class="h-4 w-4" />
      Tambah Peminjaman
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
          placeholder="Cari buku atau anggota..."
          wire:model.live.debounce.300ms="search" />
      </div>

      {{-- Total Data --}}
      <x-ui.badge color="green">
        {{ $this->loans->total() }} peminjaman
      </x-ui.badge>

      {{-- Status Filter --}}
      <x-ui.select
        name="statusFilter"
        wire:model.live="statusFilter"
        placeholder="Semua status">
        <option value="borrowed">Dipinjam</option>
        <option value="returned">Dikembalikan</option>
        <option value="overdue">Terlambat</option>
      </x-ui.select>

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
          <x-ui.table.th>
            Buku
          </x-ui.table.th>

          <x-ui.table.th>
            Member
          </x-ui.table.th>

          <x-ui.table.th sortable column="loan_date" :activeColumn="$sortColumn" :direction="$sortDirection">
            Tanggal Pinjam
          </x-ui.table.th>

          <x-ui.table.th sortable column="due_date" :activeColumn="$sortColumn" :direction="$sortDirection">
            Jatuh Tempo
          </x-ui.table.th>

          <x-ui.table.th>
            Status
          </x-ui.table.th>

          <x-ui.table.th>
            Sisa Waktu
          </x-ui.table.th>

          <x-ui.table.th align="right">
            Aksi
          </x-ui.table.th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse ($this->loans as $loan)
        <x-ui.table.tr wire:key="row-{{ $loan->id }}">
          {{-- BOOK --}}
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50">
                <x-lucide-book class="h-4 w-4" />
              </div>
              <div>
                <p
                  class="font-semibold text-slate-800">
                  {{ $loan->book->title }}
                </p>
                <p
                  class="text-xs text-slate-400">
                  {{ $loan->book->author }}
                </p>
              </div>
            </div>
          </td>

          {{-- MEMBER --}}
          <td class="px-5 py-4">
            <p
              class="font-medium text-slate-700">
              {{ $loan->member->name }}
            </p>
            <p
              class="text-xs text-slate-400">
              {{ $loan->member->member_code }}
            </p>
          </td>

          {{-- LOAN DATE --}}
          <td
            class="px-5 py-4 text-slate-500">
            {{ $loan->loan_date->format('d M Y') }}
          </td>

          {{-- DUE DATE --}}
          <td class="px-5 py-4">
            <span class="text-nowrap {{ $loan->display_status === 'overdue' ? 'font-semibold text-red-600' : 'text-slate-500' }}">
              {{ $loan->due_date->format('d M Y') }}
            </span>
          </td>

          {{-- STATUS --}}
          <td class="px-5 py-4 text-center">
            <x-ui.badge :color="$loan->status_badge['color']">
              {{ $loan->status_badge['label'] }}
            </x-ui.badge>
          </td>

          {{-- REMAINING TIME --}}
          <td class="px-5 py-4">
            <span class="{{ $loan->overdue_info['class'] }}">
              {{ $loan->overdue_info['text'] }}
            </span>
          </td>

          {{-- ACTION --}}
          <td class="px-5 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              {{-- EXTEND --}}
              @if ($loan->display_status === 'borrowed')
                <x-ui.button
                  variant="primary"
                  size="sm"
                  title="Perpanjang Peminjaman"
                  wire:click="extend({{ $loan->id }})"
                  wire:confirm="Perpanjang peminjaman selama 7 hari?">
                  <x-lucide-calendar-plus class="h-4 w-4" />
                </x-ui.button>
              @endif

              {{-- RETURN --}}
              @if ($loan->status !== 'returned')
                <x-ui.button
                  variant="secondary"
                  size="sm"
                  title="Kembalikan Buku"
                  wire:click="returnBook({{ $loan->id }})"
                  wire:confirm="Tandai buku sebagai sudah dikembalikan?">
                  <x-lucide-rotate-ccw class="h-4 w-4" />
                </x-ui.button>
              @endif

              {{-- CANCEL --}}
              @if ($loan->display_status === 'borrowed')
                <x-ui.button
                  variant="danger"
                  size="sm"
                  title="Batalkan Peminjaman"
                  wire:click="delete({{ $loan->id }})"
                  wire:confirm="Yakin ingin membatalkan peminjaman ini?">
                  <x-lucide-x class="h-4 w-4" />
                </x-ui.button>
              @endif

            </div>
          </td>
        </x-ui.table.tr>
        @empty
        <x-ui.table.empty
          icon="repeat"
          title="Data peminjaman tidak ditemukan"
          description="Belum ada data yang sesuai."
          colspan="7" />
        @endforelse
      </tbody>
    </x-ui.table>

    {{-- PAGINATION --}}
    @if ($this->loans->hasPages())
    <div class="border-t border-slate-100 px-4 py-4 sm:px-5">
      {{ $this->loans->links() }}
    </div>
    @endif

  </div>

  {{-- MODAL --}}
  @if ($showModal)
  <x-shared.modal
    title="Tambah Peminjaman"
    description="Tambahkan data peminjaman baru.">
    
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

        {{-- BOOK --}}
        <x-ui.select
          name="book_id"
          label="Buku"
          wire:model="book_id"
          placeholder="Pilih Buku"
          required>
          @foreach ($this->books as $book)
            <option value="{{ $book->id }}">
              {{ $book->title }}
              — Stok: {{ $book->stock }}
            </option>
          @endforeach
        </x-ui.select>

        {{-- MEMBER --}}
        <x-ui.select
          name="member_id"
          label="Member"
          wire:model="member_id"
          placeholder="Pilih Member"
          required>
          @foreach ($this->members as $member)
            <option value="{{ $member->id }}">
              {{ $member->name }}
              ({{ $member->member_code }})
            </option>
          @endforeach
        </x-ui.select>

        <div class="grid gap-5 sm:grid-cols-2">
          {{-- LOAN DATE --}}
          <x-ui.input
            name="loan_date"
            label="Tanggal Pinjam"
            type="date"
            wire:model="loan_date"
            required />
          {{-- DUE DATE --}}
          <x-ui.input
            name="due_date"
            label="Tanggal Jatuh Tempo"
            type="date"
            wire:model="due_date"
            required />
        </div>

        {{-- NOTES --}}
        <x-ui.textarea
          name="notes"
          label="Catatan"
          placeholder="Tambahkan catatan jika diperlukan..."
          rows="4"
          wire:model="notes" />

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
            Simpan Peminjaman
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