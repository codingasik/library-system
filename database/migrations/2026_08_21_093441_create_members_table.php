<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

  // database/migrations/..._create_members_table.php
  public function up(): void
  {
    Schema::create('members', function (Blueprint $table) {
      $table->id();
      $table->string('member_code', 20)->unique();
      $table->string('name', 150);
      $table->string('email')->unique()->nullable();
      $table->string('phone', 20)->nullable();
      $table->text('address')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('members');
  }
};
