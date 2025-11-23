<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studenti', function (Blueprint $table) {
            $table->id();
            $table->string('ime');
            $table->string('prezime');
            $table->string('br_indexa')->unique();
            $table->date('datum_rodjenja');
            $table->string('telefon');
            $table->string('email')->unique();
            $table->integer('godina_studija');
            $table->string('jmbg')->unique();
            $table->foreignId('nivo_studija_id')
                  ->constrained('nivo_studija')
                  ->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studenti');
    }
};
