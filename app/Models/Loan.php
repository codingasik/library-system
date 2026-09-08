<?php
// app/Models/Loan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
  use HasFactory;

  protected $fillable = [
    'book_id',
    'member_id',
    'loan_date',
    'due_date',
    'return_date',
    'status',
    'notes',
  ];

  // Penulisan casts standar Laravel 13
  protected function casts(): array
  {
    return [
      'loan_date'   => 'date',
      'due_date'    => 'date',
      'return_date' => 'date',
    ];
  }

  public function book(): BelongsTo
  {
    return $this->belongsTo(Book::class);
  }

  public function member(): BelongsTo
  {
    return $this->belongsTo(Member::class);
  }

  public function isOverdue(): bool
  {
    return $this->status === 'borrowed'
      && $this->due_date->isPast()
      && !$this->due_date->isToday();
  }

  // Accessor modern
  protected function displayStatus(): Attribute
  {
    return Attribute::make(
      get: function () {
        if ($this->status === 'returned') {
          return 'returned';
        }

        return $this->isOverdue() ? 'overdue' : 'borrowed';
      }
    );
  }

  // Ambil atribut konfigurasi status
  public function getStatusBadgeAttribute(): array
  {
    return match ($this->display_status) {
      'borrowed' => ['color' => 'blue', 'label' => 'Dipinjam'],
      'returned' => ['color' => 'green', 'label' => 'Dikembalikan'],
      'overdue'  => ['color' => 'red', 'label' => 'Terlambat'],
      default    => ['color' => 'gray', 'label' => $this->display_status],
    };
  }

  public function getOverdueInfoAttribute(): array
  {
    if ($this->status === 'returned') {
      return ['text' => '—', 'class' => 'text-slate-400'];
    }

    $days = (int) now()->diffInDays($this->due_date);

    if ($this->display_status === 'overdue') {
      return [
        'text' => "Terlambat {$days} hari",
        'class' => 'font-medium text-red-600',
      ];
    }

    return [
      'text' => "{$days} hari lagi",
      'class' => 'font-medium text-amber-600',
    ];
  }
}
