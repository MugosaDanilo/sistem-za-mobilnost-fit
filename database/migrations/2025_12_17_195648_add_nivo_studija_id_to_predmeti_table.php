<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('predmeti', function (Blueprint $table) {
            $table->foreignId('nivo_studija_id')->nullable()->constrained('nivo_studija');
        });

        // Get ID for 'Osnovne'
        $osnovne = DB::table('nivo_studija')->where('naziv', 'Osnovne')->first();

        if ($osnovne) {
            DB::table('predmeti')->update(['nivo_studija_id' => $osnovne->id]);
        }

        // Optionally make it not nullable after update, but leaving nullable is safer for now if seeder fails
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predmeti', function (Blueprint $table) {
            $table->dropForeign(['nivo_studija_id']);
            $table->dropColumn('nivo_studija_id');
        });
    }
};
