<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app')]
#[Title('Dashboard')]
class extends Component
{
  #[Computed]
  public function totalBooks()
  {
    return Book::count();
  }

  #[Computed]
  public function activeMembers()
  {
    return Member::where('is_active', true)->count();
  }

  // FIX: Menggunakan Query Database Murni (Bukan Loan::all())
  #[Computed]
  public function borrowedLoans()
  {
    return Loan::query()
      ->where('status', 'borrowed')
      ->whereDate('due_date', '>=', today())
      ->count();
  }

  // FIX: Menggunakan Query Database Murni (Bukan Loan::all())
  #[Computed]
  public function overdueLoans()
  {
    return Loan::query()
      ->where('status', 'borrowed')
      ->whereDate('due_date', '<', today())
      ->count();
  }

  #[Computed]
  public function recentLoans()
  {
    return Loan::query()
      ->with(['book', 'member'])
      ->latest()
      ->take(5)
      ->get();
  }
}; 

?>

<div class="space-y-6">

  {{-- HEADER --}}
  <x-shared.page-header
    title="Dashboard"
    description="Ringkasan aktivitas dan performa perpustakaan.">
    <x-slot:icon>
      <x-lucide-layout-dashboard class="h-6 w-6 text-indigo-600" />
    </x-slot:icon>
  </x-shared.page-header>

  {{-- STATS CARDS --}}
  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Total Buku --}}
    <x-shared.stat-card
      title="Total Buku"
      :value="$this->totalBooks"
      color="indigo">
      <x-slot:icon>
        <x-lucide-book class="h-5 w-5" />
      </x-slot:icon>
    </x-shared.stat-card>

    {{-- Anggota Aktif --}}
    <x-shared.stat-card
      title="Anggota Aktif"
      :value="$this->activeMembers"
      color="emerald">
      <x-slot:icon>
        <x-lucide-users class="h-5 w-5" />
      </x-slot:icon>
    </x-shared.stat-card>

    {{-- Sedang Dipinjam --}}
    <x-shared.stat-card
      title="Sedang Dipinjam"
      :value="$this->borrowedLoans"
      color="amber">
      <x-slot:icon>
        <x-lucide-book-open class="h-5 w-5" />
      </x-slot:icon>
    </x-shared.stat-card>

    {{-- Terlambat --}}
    <x-shared.stat-card
      title="Terlambat"
      :value="$this->overdueLoans"
      color="rose">
      <x-slot:icon>
        <x-lucide-alert-triangle class="h-5 w-5" />
      </x-slot:icon>
    </x-shared.stat-card>

  </div>

  {{-- RECENT LOANS --}}
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    <div class="border-b border-slate-100 px-5 py-4">
      <h3 class="font-semibold text-slate-800">
        Peminjaman Terbaru
      </h3>
    </div>

    <x-ui.table>
      <thead class="border-b border-slate-200 bg-slate-50">
        <tr>
          <x-ui.table.th>Buku</x-ui.table.th>
          <x-ui.table.th>Anggota</x-ui.table.th>
          <x-ui.table.th>Jatuh Tempo</x-ui.table.th>
          <x-ui.table.th align="center">Status</x-ui.table.th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse ($this->recentLoans as $loan)
        <x-ui.table.tr wire:key="recent-{{ $loan->id }}">
          
          {{-- BOOK --}}
          <td class="px-5 py-4 font-medium text-slate-800">
            {{ $loan->book->title }}
          </td>

          {{-- MEMBER --}}
          <td class="px-5 py-4 text-slate-600">
            {{ $loan->member->name }}
          </td>

          {{-- DUE DATE --}}
          <td class="px-5 py-4 text-slate-500">
            {{ $loan->due_date->format('d M Y') }}
          </td>

          {{-- STATUS --}}
          <td class="px-5 py-4 text-center">
            <x-ui.badge :color="$loan->status_badge['color']">
              {{ $loan->status_badge['label'] }}
            </x-ui.badge>
          </td>

        </x-ui.table.tr>
        @empty
        <x-ui.table.empty
          icon="repeat"
          title="Belum ada transaksi"
          description="Aktivitas peminjaman terbaru akan muncul di sini."
          colspan="4" />
        @endforelse
      </tbody>
    </x-ui.table>

  </div>

</div>