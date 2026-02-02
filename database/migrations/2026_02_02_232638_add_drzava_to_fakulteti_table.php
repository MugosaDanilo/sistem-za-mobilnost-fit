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
        Schema::table('fakulteti', function (Blueprint $table) {
            $table->string('drzava')->nullable()->after('naziv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fakulteti', function (Blueprint $table) {
            $table->dropColumn('drzava');
        });
    }
};
