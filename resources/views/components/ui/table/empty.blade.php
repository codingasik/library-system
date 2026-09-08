@props([
  'icon' => 'inbox',
  'title' => 'Data tidak ditemukan',
  'description' => 'Coba ubah kata kunci pencarian Anda.',
  'colspan' => 1,
])

<tr>
  <td colspan="{{ $colspan }}" class="px-5 py-16 text-center">
    <div class="flex flex-col items-center justify-center">
      <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
        <x-dynamic-component :component="'lucide-' . $icon" class="h-6 w-6" />
      </div>
      <p class="font-semibold text-slate-700 text-sm">{{ $title }}</p>
      <p class="mt-1 text-xs text-slate-400 max-w-xs">{{ $description }}</p>
    </div>
  </td>
</tr>