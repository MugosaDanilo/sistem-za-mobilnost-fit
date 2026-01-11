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
        Schema::table('predmeti', function (Blueprint $table) {
            $table->string('sifra_predmeta')->before('naziv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predmeti', function (Blueprint $table) {
            $table->dropColumn('sifra_predmeta');
        });
    }
};
