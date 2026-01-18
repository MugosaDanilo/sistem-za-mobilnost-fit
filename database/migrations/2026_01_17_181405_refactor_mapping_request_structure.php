<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mapping_request_subjects', function (Blueprint $table) {
            $table->foreignId('professor_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        $requests = DB::table('mapping_requests')->get();
        foreach ($requests as $request) {
            DB::table('mapping_request_subjects')
                ->where('mapping_request_id', $request->id)
                ->update(['professor_id' => $request->professor_id]);
        }
        
        Schema::table('mapping_requests', function (Blueprint $table) {
            $table->foreignId('professor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse
        Schema::table('mapping_request_subjects', function (Blueprint $table) {
            $table->dropForeign(['professor_id']);
            $table->dropColumn('professor_id');
        });

    }
};
