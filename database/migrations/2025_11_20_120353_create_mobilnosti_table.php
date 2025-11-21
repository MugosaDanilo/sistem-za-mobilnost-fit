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
        Schema::create('mobilnosti', function (Blueprint $table) {
            $table->id();
            $table->date('datum_pocetka');
            $table->date('datum_kraja');

            $table->foreignId('student_id')
                  ->constrained('studenti')
                  ->onDelete('restrict');

            $table->foreignId('fakultet_id')
                  ->constrained('fakulteti')
                  ->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobilnosti');
    }
};
