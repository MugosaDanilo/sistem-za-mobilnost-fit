<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Broj bodova uz ocjenu u learning agreement-u (opciono).
 * Šalje se u karton studenta na platformi kao student_pfs.ukupanBrojBodova.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_agreements', function (Blueprint $table) {
            if (!Schema::hasColumn('learning_agreements', 'bodovi')) {
                $table->decimal('bodovi', 5, 2)->nullable()->after('ocjena');
            }
        });
    }

    public function down(): void
    {
        Schema::table('learning_agreements', function (Blueprint $table) {
            $table->dropColumn('bodovi');
        });
    }
};
