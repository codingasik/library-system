<?php
// app/Models/Book.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
  use HasFactory;

  protected $fillable = [
    'category_id',
    'title',
    'author',
    'isbn',
    'publisher',
    'year',
    'stock',
    'cover',
    'description',
  ];

  // Type casting menggunakan method bawaan Laravel 13.
  protected function casts(): array
  {
    return [
      'year'  => 'integer',
      'stock' => 'integer',
    ];
  }

  // Relasi ke model Category (Belongs To).
  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  // Relasi ke model Loan (One to Many).
  public function loans(): HasMany
  {
    return $this->hasMany(Loan::class);
  }

  // Helper untuk menghitung sisa stok yang tersedia saat ini.
  public function availableStock(): int
  {
    $borrowedCount = $this->loans()->where('status', 'borrowed')->count();
    return max(0, $this->stock - $borrowedCount);
  }
}
