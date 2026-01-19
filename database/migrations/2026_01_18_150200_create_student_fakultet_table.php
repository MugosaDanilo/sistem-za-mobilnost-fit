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
        Schema::create('student_fakultet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('studenti')->onDelete('cascade');
            $table->foreignId('fakultet_id')->constrained('fakulteti')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint to prevent duplicate entries
            $table->unique(['student_id', 'fakultet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fakultet');
    }
};
