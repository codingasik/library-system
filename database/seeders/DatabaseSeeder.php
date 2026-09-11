<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;
  // database/seeders/DatabaseSeeder.php
  public function run(): void
  {
    // Buat 1 user admin default
    \App\Models\User::updateOrCreate(
    ['email' => env('ADMIN_EMAIL', 'admin@library.com')],
      [
        'name' => 'Admin Perpustakaan',
        'password' => bcrypt(env('ADMIN_PASSWORD', 'admin123')),
      ]
    );
    $this->call([
      CategorySeeder::class, // harus pertama
      BookSeeder::class,     // butuh category
      MemberSeeder::class,
      LoanSeeder::class,     // butuh book & member
    ]);
  }
}
