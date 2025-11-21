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
        Schema::create('prepis_agreements', function (Blueprint $table) {
            $table->id();

            $table->enum('status', ['u procesu', 'odobren', 'odbijen'])->default('u procesu');

            $table->foreignId('prepis_id')
                  ->constrained('prepisi')
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
        Schema::dropIfExists('prepis_agreements');
    }
};
