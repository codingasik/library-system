<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
  // database/factories/MemberFactory.php
  public function definition(): array
  {
    return [
      // Menggunakan unique numerik acak/sekuensial tanpa variabel statis
      'member_code' => 'M-' . fake()->unique()->numerify('####'),
      'name'        => fake()->name(),
      'email'       => fake()->unique()->safeEmail(),
      'phone'       => fake()->phoneNumber(),
      'address'     => fake()->address(),
      'is_active'   => fake()->boolean(85),
    ];
  }
}
