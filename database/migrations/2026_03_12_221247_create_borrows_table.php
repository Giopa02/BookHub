<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->date('borrowing_date');
            $table->date('return_date')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('borrow_copy', function (Blueprint $table) {
            $table->foreignId('borrow_id')->constrained('borrows')->onDelete('cascade');
            $table->foreignId('copy_id')->constrained('copies')->onDelete('cascade');
            $table->primary(['borrow_id', 'copy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_copy');
        Schema::dropIfExists('borrows');
    }
};