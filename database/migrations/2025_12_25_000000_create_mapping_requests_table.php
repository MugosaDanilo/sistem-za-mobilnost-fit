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
        Schema::create('mapping_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fakultet_id')->constrained('fakulteti')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamps();
        });

        Schema::create('mapping_request_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapping_request_id')->constrained('mapping_requests')->onDelete('cascade');
            $table->foreignId('strani_predmet_id')->constrained('predmeti')->onDelete('cascade');
            $table->foreignId('fit_predmet_id')->nullable()->constrained('predmeti')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_request_subjects');
        Schema::dropIfExists('mapping_requests');
    }
};
