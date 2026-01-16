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
        Schema::table('studenti', function (Blueprint $table) {
            $table->foreignId('fakultet_id')->nullable()->constrained('fakulteti')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('studenti', function (Blueprint $table) {
            $table->dropForeign(['fakultet_id']);
            $table->dropColumn('fakultet_id');
        });
    }
};
