<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // database/migrations/..._create_loans_table.php
  public function up(): void
  {
    Schema::create('loans', function (Blueprint $table) {
      $table->id();
      $table->foreignId('book_id')->constrained()->restrictOnDelete();
      $table->foreignId('member_id')->constrained()->restrictOnDelete();
      $table->date('loan_date');
      $table->date('due_date');
      $table->date('return_date')->nullable();
      $table->enum('status', ['borrowed', 'returned'])->default('borrowed');
      $table->text('notes')->nullable();
      $table->timestamps();

      // Indeks untuk pencarian cepat & laporan
      $table->index(['member_id', 'status']);  // Cek peminjaman aktif per member
      $table->index(['status', 'due_date']);   // Cek daftar peminjaman terlambat (overdue)
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('loans');
  }
};
