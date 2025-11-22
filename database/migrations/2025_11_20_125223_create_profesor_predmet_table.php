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
        Schema::create('profesor_predmet', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profesor_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('predmet_id')
                  ->constrained('predmeti')
                  ->onDelete('cascade');

            $table->timestamps();

            // Unikatni indeks da profesor ne može imati isti predmet više puta
            $table->unique(['profesor_id', 'predmet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesor_predmet');
    }
};
