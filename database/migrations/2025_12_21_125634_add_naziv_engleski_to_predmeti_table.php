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
            $table->string('naziv_engleski')->nullable()->after('naziv');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predmeti', function (Blueprint $table) {
            $table->dropColumn('naziv_engleski');
        });
    }
};
