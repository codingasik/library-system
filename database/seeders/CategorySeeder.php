<?php

namespace Database\Seeders;
// Jangan lupa import Model Category
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  // database/seeders/CategorySeeder.php
  public function run(): void
  {
    $categories = [
      ['name' => 'Fiction',     'description' => 'Novels, short stories, and literary fiction'],
      ['name' => 'Non-Fiction', 'description' => 'Biographies, memoirs, and true accounts'],
      ['name' => 'Science',     'description' => 'Scientific discoveries and research'],
      ['name' => 'Technology',  'description' => 'Computers, software, and engineering'],
      ['name' => 'History',     'description' => 'World history and civilizations'],
      ['name' => 'Philosophy',  'description' => 'Ethics, logic, and metaphysics'],
      ['name' => 'Children',    'description' => 'Books for young readers'],
      ['name' => 'Reference',   'description' => 'Dictionaries, encyclopedias, and atlases'],
    ];

    // Mass insert dengan timestamp otomatis via upsert / insert
    foreach ($categories as $data) {
      Category::firstOrCreate(['name' => $data['name']], $data);
    }
  }
}
