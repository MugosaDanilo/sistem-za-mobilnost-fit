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
        Schema::table('mobilnost_dokumenti', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('mobility_categories')->nullOnDelete();
        });

        // Update existing documents to have Default category
        $defaultCategory = DB::table('mobility_categories')->where('name', 'Default')->first();
        if ($defaultCategory) {
            DB::table('mobilnost_dokumenti')->update(['category_id' => $defaultCategory->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobilnost_dokumenti', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
