<?php

namespace Database\Factories;

// Jangan lupa import Model Category
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
  // database/factories/BookFactory.php
  public function definition(): array
  {
    return [
      // Laravel otomatis menggunakan kategori yang ada / membuatkan jika belum ada
      'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
      'title'       => fake()->sentence(4),
      'author'      => fake()->name(),
      'isbn'        => fake()->unique()->isbn13(),
      'publisher'   => fake()->company(),
      'year'        => fake()->numberBetween(1990, 2024),
      'stock'       => fake()->numberBetween(1, 5),
      'cover'       => null,
      'description' => fake()->paragraph(2),
    ];
  }
}
