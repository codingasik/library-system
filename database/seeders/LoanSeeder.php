<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Jangan lupa import Modelnya
use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;

class LoanSeeder extends Seeder
{

  // database/seeders/LoanSeeder.php — data peminjaman dumm
  public function run(): void
  {
    $books   = Book::pluck('id');
    $members = Member::where('is_active', true)->pluck('id');

    if ($books->isEmpty() || $members->isEmpty()) {
      return;
    }

    // 1. 15 Peminjaman Selesai (returned)
    for ($i = 0; $i < 15; $i++) {
      $loanDate = fake()->dateTimeBetween('-3 months', '-1 week');
      Loan::create([
        'book_id'     => $books->random(),
        'member_id'   => $members->random(),
        'loan_date'   => $loanDate,
        'due_date'    => (clone $loanDate)->modify('+7 days'),
        'return_date' => (clone $loanDate)->modify('+5 days'),
        'status'      => 'returned',
      ]);
    }

    // 2. 8 Peminjaman Aktif (borrowed - belum lewat tenggat)
    for ($i = 0; $i < 8; $i++) {
      $loanDate = fake()->dateTimeBetween('-5 days', 'now');
      Loan::create([
        'book_id'   => $books->random(),
        'member_id' => $members->random(),
        'loan_date' => $loanDate,
        'due_date'  => (clone $loanDate)->modify('+7 days'),
        'status'    => 'borrowed',
      ]);
    }

    // 3. 4 Peminjaman Terlambat (status 'borrowed', tetapi tanggal due_date sudah lewat)
    for ($i = 0; $i < 4; $i++) {
      $loanDate = fake()->dateTimeBetween('-1 month', '-2 weeks');
      Loan::create([
        'book_id'   => $books->random(),
        'member_id' => $members->random(),
        'loan_date' => $loanDate,
        'due_date'  => (clone $loanDate)->modify('+7 days'),
        'status'    => 'borrowed', // PERBAIKAN: Tetap 'borrowed' di DB
      ]);
    }
  }
}
