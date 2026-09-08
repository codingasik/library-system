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
  $this->call([
    CategorySeeder::class, // harus pertama
    BookSeeder::class,     // butuh category
    MemberSeeder::class,
    LoanSeeder::class,     // butuh book & member
  ]);
}
}
