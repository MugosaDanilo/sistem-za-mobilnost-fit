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
        Schema::table('mobilnosti', function (Blueprint $table) {
            $table->string('studijska_godina')->nullable()->after('tip_mobilnosti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobilnosti', function (Blueprint $table) {
            $table->dropColumn('studijska_godina');
        });
    }
};
