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
    Schema::table('prepis_agreements', function (Blueprint $table) {
        $table->foreignId('nastavna_lista_id')
              ->nullable()
              ->after('strani_predmet_id')
              ->constrained('nastavne_liste')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('prepis_agreements', function (Blueprint $table) {
        $table->dropForeign(['nastavna_lista_id']);
        $table->dropColumn('nastavna_lista_id');
    });
}

};
