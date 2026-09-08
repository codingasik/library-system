<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
  // database/factories/CategoryFactory.php
  public function definition(): array
  {
    return [
      'name'        => fake()->unique()->words(2, true),
      'description' => fake()->sentence(),
    ];
  }
}
