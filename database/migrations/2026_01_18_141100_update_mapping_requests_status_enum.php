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
        
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE mapping_requests DROP CONSTRAINT IF EXISTS mapping_requests_status_check");
            DB::statement("ALTER TABLE mapping_requests ADD CONSTRAINT mapping_requests_status_check CHECK (status::text = ANY (ARRAY['pending'::text, 'accepted'::text, 'rejected'::text, 'completed'::text]))");
        } else {
            Schema::table('mapping_requests', function (Blueprint $table) {
                $table->enum('status', ['pending', 'accepted', 'rejected', 'completed'])->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement("ALTER TABLE mapping_requests DROP CONSTRAINT IF EXISTS mapping_requests_status_check");
            
            DB::table('mapping_requests')
                ->whereNotIn('status', ['pending', 'completed'])
                ->update(['status' => 'pending']);

            DB::statement("ALTER TABLE mapping_requests ADD CONSTRAINT mapping_requests_status_check CHECK (status::text = ANY (ARRAY['pending'::text, 'completed'::text]))");
        } else {
            // ...
        }
    }
};
