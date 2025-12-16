<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('studenti', function (Blueprint $table) {
            $table->boolean('pol')->default(false)->after('jmbg');
        });
    }

    public function down(): void
    {
        Schema::table('studenti', function (Blueprint $table) {
            $table->dropColumn('pol');
        });
    }
};
