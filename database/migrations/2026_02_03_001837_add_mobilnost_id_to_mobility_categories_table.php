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
        Schema::table('mobility_categories', function (Blueprint $table) {
            $table->foreignId('mobilnost_id')->nullable()->constrained('mobilnosti')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobility_categories', function (Blueprint $table) {
            $table->dropForeign(['mobilnost_id']);
            $table->dropColumn('mobilnost_id');
        });
    }
};
