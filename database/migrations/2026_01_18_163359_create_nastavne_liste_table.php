<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nastavne_liste', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('predmet_id');
            $table->unsignedBigInteger('fakultet_id');
            $table->string('link');
            $table->timestamps();

            $table->foreign('predmet_id')->references('id')->on('predmeti')->onDelete('cascade');
            $table->foreign('fakultet_id')->references('id')->on('fakulteti')->onDelete('cascade');
            $table->unique(['predmet_id', 'fakultet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nastavne_liste');
    }
};
