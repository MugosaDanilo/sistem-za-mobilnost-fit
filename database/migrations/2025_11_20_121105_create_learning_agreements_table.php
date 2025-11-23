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
        Schema::create('learning_agreements', function (Blueprint $table) {
            $table->id();
            $table->text('napomena')->nullable();

            $table->string('ocjena')->nullable();
            
            $table->foreignId('mobilnost_id')
                  ->constrained('mobilnosti')->onDelete('cascade')
                  ->onDelete('cascade');

            $table->foreignId('fit_predmet_id')
                  ->constrained('predmeti')
                  ->onDelete('cascade');

            $table->foreignId('strani_predmet_id')
                  ->constrained('predmeti')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_agreements');
    }
};
