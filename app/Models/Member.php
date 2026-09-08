<?php
// app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
  use HasFactory;

  protected $fillable = [
    'member_code',
    'name',
    'email',
    'phone',
    'address',
    'is_active',
  ];

  // Type casting menggunakan method bawaan Laravel 13.
  protected function casts(): array
  {
    return [
      'is_active' => 'boolean',
    ];
  }

  // Relasi ke model Loan (One to Many).
  public function loans(): HasMany
  {
    return $this->hasMany(Loan::class);
  }

  // Scope kueri untuk mengambil member yang aktif saja.
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }
}
