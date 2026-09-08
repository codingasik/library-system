@props([
  'title' => "Judul Halaman",
  'description' => null,
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <div class="flex items-center gap-2">
    @if($icon)
    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-xl">
      {{ $icon }}
    </div>
    @endif

    <div>
      <h1 class="text-xl font-bold text-slate-800">
        {{ $title }}
      </h1>

      <p class="text-sm text-slate-500">
        {{ $description }}
      </p>
    </div>
  </div>

  {{ $slot }}

</div>
