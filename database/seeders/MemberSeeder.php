<?php

namespace Database\Seeders;
// Jangan lupa import Model Member
use App\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
  // database/seeders/MemberSeeder.php
  public function run(): void
  {
    Member::factory(20)->create();
  }
}
