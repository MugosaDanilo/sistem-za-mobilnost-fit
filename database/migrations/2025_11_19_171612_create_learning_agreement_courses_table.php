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
        Schema::create('learning_agreement_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_agreement_id')->constrained()->onDelete('cascade');
            $table->string('predmet_fit');
            $table->string('semestar')->nullable();
            $table->string('ects')->nullable();
            $table->string('strani_predmet');
            $table->string('ocjena')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_agreement_courses');
    }
};
