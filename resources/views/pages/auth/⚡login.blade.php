<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest')]
#[Title('Login - Library System')]
class extends Component
{
  public $email = '';
  public $password = '';
  public $remember = false;

  protected function rules()
  {
    return [
      'email' => 'required|email',
      'password' => 'required',
    ];
  }

  public function login()
  {
    $credentials = $this->validate();

    if (Auth::attempt($credentials, $this->remember)) {
      session()->regenerate();

      return $this->redirectIntended(route('dashboard'), navigate: true);
    }

    $this->addError('email', 'Kredensial yang dimasukkan tidak sesuai.');
  }
};

?>

<div class="min-h-screen flex items-center justify-center bg-slate-50/60 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 transition-all">
    
    {{-- Header Section --}}
    <div class="text-center">
      <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 mb-4 shadow-inner">
        <x-lucide-book-open class="w-6 h-6" />
      </div>
      <h2 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Akun</h2>
      <p class="mt-1.5 text-sm text-slate-500">Sistem Informasi Perpustakaan Coding Asik</p>
      
      {{-- Dev Notice Badge --}}
      <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
        <x-lucide-info class="w-3.5 h-3.5 text-amber-500 shrink-0" />
        <span>Kredensial admin diatur via file <code class="font-semibold bg-amber-100/80 px-1 py-0.5 rounded text-amber-800">.env</code></span>
      </div>
    </div>

    {{-- Form Section --}}
    <form class="mt-8 space-y-5" wire:submit="login">
      <div class="space-y-4">
        <x-ui.input
          name="email"
          label="Email"
          type="email"
          placeholder="admin@library.com"
          wire:model="email"
          required />

        <x-ui.input
          name="password"
          label="Password"
          type="password"
          placeholder="••••••••"
          wire:model="password"
          required />
      </div>

      <div class="flex items-center justify-between pt-1">
        <label class="inline-flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
          <input 
            type="checkbox" 
            wire:model="remember" 
            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0 transition cursor-pointer">
          <span>Ingat Saya</span>
        </label>
      </div>

      <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" class="w-full justify-center py-2.5 shadow-md shadow-indigo-500/10">
        {{-- Icon Spinner (Hanya muncul saat loading) --}}
        <x-lucide-loader-2 wire:loading wire:target="login" class="w-4 h-4 animate-spin mr-2 shrink-0" />

        {{-- Teks Tombol --}}
        <span wire:loading.remove wire:target="login">Masuk Sekarang</span>
        <span wire:loading wire:target="login">Memproses...</span>
      </x-ui.button>
    </form>
    
  </div>
</div>