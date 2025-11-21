<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('predmeti', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->integer('ects');
            $table->integer('semestar');

            $table->foreignId('fakultet_id')
                ->constrained('fakulteti')
                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predmeti');
    }
};
