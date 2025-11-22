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
        Schema::create('fakultet_predmet', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fakultet_id')
                  ->constrained('fakulteti')
                  ->onDelete('cascade');

            $table->foreignId('predmet_id')
                  ->constrained('predmeti')
                  ->onDelete('cascade');

            $table->timestamps();

            // Unikatni indeks da isti predmet ne može biti više puta vezan za isti fakultet
            $table->unique(['fakultet_id', 'predmet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakultet_predmet');
    }
};
