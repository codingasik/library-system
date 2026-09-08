<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard')->name('dashboard');

Route::livewire('/books', 'pages::books.index')
  ->name('books.index');

Route::livewire('/books/{book}', 'pages::books.show')->name('books.show');

Route::livewire('/members', 'pages::members.index')
  ->name('members.index');

Route::livewire('/categories', 'pages::categories.index')
  ->name('categories.index');

Route::livewire('/loans', 'pages::loans.index')
  ->name('loans.index');

//Route::get('/', fn() => view('dashboard'))->name('dashboard');

// Akan diganti satu per satu mulai Hari 27
//Route::get('/books',      fn() => 'Coming soon')->name('books.index');
//Route::get('/members',    fn() => 'Coming soon')->name('members.index');
//Route::get('/loans',      fn() => 'Coming soon')->name('loans.index');
//Route::get('/categories', fn() => 'Coming soon')->name('categories.index');
