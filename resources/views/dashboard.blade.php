@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')
<div class="space-y-6">
  {{-- Tambahkan parameter --}}
  <livewire:counter :start="10"/>
  <livewire:book-search />
  <livewire:quick-category-form />
</div>
@endsection