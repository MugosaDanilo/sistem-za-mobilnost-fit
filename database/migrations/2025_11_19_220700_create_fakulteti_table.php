<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fakulteti', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->string('email')->unique();
            $table->string('telefon');
            $table->string('web')->nullable();
            $table->text('uputstvo_za_ocjene')->nullable();
            $table->foreignId('univerzitet_id')
                  ->constrained('univerziteti')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fakulteti');
    }
};
