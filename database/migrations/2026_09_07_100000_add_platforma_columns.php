<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Veza sa studentskom platformom (platforma-united).
 * - studenti/predmeti/fakulteti dobijaju referencu na zapis sa platforme
 * - mobilnosti/mapping_requests čuvaju status slanja priznatih ispita
 */
return new class extends Migration
{
    public function up(): void
    {
        $indeksi = Schema::getIndexListing('studenti');

        Schema::table('studenti', function (Blueprint $table) use ($indeksi) {
            if (!Schema::hasColumn('studenti', 'platforma_student_id')) {
                $table->unsignedBigInteger('platforma_student_id')->nullable()->after('id');
                $table->unsignedBigInteger('platforma_upis_id')->nullable()->after('platforma_student_id');
                $table->timestamp('platforma_synced_at')->nullable()->after('platforma_upis_id');
            }
            // Jedan lokalni student = student + upis na platformi (isti student može biti na osnovnim i masteru).
            if (in_array('studenti_platforma_student_id_unique', $indeksi, true)) {
                $table->dropUnique('studenti_platforma_student_id_unique');
            }
            if (!in_array('studenti_platforma_student_upis_unique', $indeksi, true)) {
                $table->unique(['platforma_student_id', 'platforma_upis_id'], 'studenti_platforma_student_upis_unique');
            }
            // Platforma nema datum rođenja ni obavezan telefon/email; studenti sa platforme ih mogu nemati.
            $table->date('datum_rodjenja')->nullable()->change();
            $table->string('telefon')->nullable()->change();
            $table->string('email')->nullable()->change();
        });

        Schema::table('predmeti', function (Blueprint $table) {
            if (!Schema::hasColumn('predmeti', 'platforma_pfs_id')) {
                $table->unsignedBigInteger('platforma_pfs_id')->nullable()->unique()->after('id')
                    ->comment('predmet_fakultet_semestar.id na platformi');
                $table->unsignedBigInteger('platforma_predmet_id')->nullable()->after('platforma_pfs_id');
            }
        });

        Schema::table('fakulteti', function (Blueprint $table) {
            if (!Schema::hasColumn('fakulteti', 'platforma_fakultet_id')) {
                $table->unsignedBigInteger('platforma_fakultet_id')->nullable()->unique()->after('id');
            }
        });

        foreach (['mobilnosti', 'mapping_requests'] as $t) {
            Schema::table($t, function (Blueprint $table) use ($t) {
                if (!Schema::hasColumn($t, 'platforma_poslato_at')) {
                    $table->timestamp('platforma_poslato_at')->nullable();
                    $table->text('platforma_odgovor')->nullable();
                    $table->text('platforma_greska')->nullable();
                }
            });
        }

        // Matični fakultet (FIT) -> platforma
        DB::table('fakulteti')
            ->where('naziv', 'FIT')
            ->whereNull('platforma_fakultet_id')
            ->update(['platforma_fakultet_id' => (int) env('PLATFORMA_MATICNI_FAKULTET_ID', 5)]);
    }

    public function down(): void
    {
        $indeksi = Schema::getIndexListing('studenti');
        Schema::table('studenti', function (Blueprint $table) use ($indeksi) {
            foreach (['studenti_platforma_student_upis_unique', 'studenti_platforma_student_id_unique'] as $idx) {
                if (in_array($idx, $indeksi, true)) {
                    $table->dropUnique($idx);
                }
            }
            $table->dropColumn(['platforma_student_id', 'platforma_upis_id', 'platforma_synced_at']);
        });
        Schema::table('predmeti', function (Blueprint $table) {
            $table->dropUnique(['platforma_pfs_id']);
            $table->dropColumn(['platforma_pfs_id', 'platforma_predmet_id']);
        });
        Schema::table('fakulteti', function (Blueprint $table) {
            $table->dropUnique(['platforma_fakultet_id']);
            $table->dropColumn('platforma_fakultet_id');
        });
        foreach (['mobilnosti', 'mapping_requests'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn(['platforma_poslato_at', 'platforma_odgovor', 'platforma_greska']);
            });
        }
    }
};
