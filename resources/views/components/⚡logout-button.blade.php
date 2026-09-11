<?php

use Livewire\Component;

new class extends Component
{
  public function logout()
  {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return $this->redirect(route('login'));
  }
};
?>

<a 
  wire:click="logout" 
  type="button"
  class="nav-link flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors text-slate-300 hover:bg-slate-800 hover:text-white cursor-pointer">
  <x-lucide-log-out class="h-4 w-4" />
  <span class="sidebar-text whitespace-nowrap">Logout</span>
</a>