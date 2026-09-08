<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // database/migrations/..._create_books_table.php
  public function up(): void
  {
    Schema::create('books', function (Blueprint $table) {
      $table->id();
      $table->foreignId('category_id')->constrained()->restrictOnDelete();
      $table->string('title', 200);
      $table->string('author', 150);
      $table->string('isbn', 20)->unique()->nullable();
      $table->string('publisher', 100)->nullable();
      $table->unsignedSmallInteger('year')->nullable();
      $table->unsignedInteger('stock')->default(0);
      $table->string('cover')->nullable();
      $table->text('description')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('books');
  }
};
