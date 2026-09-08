<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use App\Models\Predmet;
use Illuminate\Database\Seeder;

use App\Services\SubjectImportService;
use Illuminate\Support\Facades\Artisan;

class PredmetiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $importer = new SubjectImportService();
        $basic = NivoStudija::where('naziv', 'Osnovne')->first();
        $master = NivoStudija::where('naziv', 'Master')->first();

        // Matični (FIT) predmeti: ako je platforma konfigurisana, povlače se sa nje.
        // Excel iz storage/app/predmeti služi samo kad platforma nije dostupna.
        if (config('platforma.enabled')) {
            $this->command?->info('Matični predmeti se sinhronizuju sa studentske platforme...');
            $exit = Artisan::call('platforma:sync-predmeti');
            $this->command?->line(trim(Artisan::output()));
            if ($exit !== 0) {
                $this->command?->warn('Sinhronizacija nije uspjela, FIT predmeti nisu ubačeni. Pokreni kasnije: php artisan platforma:sync-predmeti');
            }
        } else {
            $this->seedFitIzExcela($importer, $basic, $master);
        }

        $this->seedStraneFakultete($importer, $basic);
    }

    private function seedFitIzExcela(SubjectImportService $importer, ?NivoStudija $basic, ?NivoStudija $master): void
    {
        $fitPath = storage_path('app/predmeti/FIT_Nastavni_planovi_1.xlsx');
        if (!file_exists($fitPath)) {
            return;
        }

        $coursesFitBasic = $importer->loadCoursesFit($fitPath, 'basic');
        $coursesFitMaster = $importer->loadCoursesFit($fitPath, 'master');

        $unimed = Fakultet::where('naziv', 'LIKE', '%FIT%')
                        ->orWhere('naziv', 'LIKE', '%Fakultet za informacione tehnologije%')
                        ->first();

        foreach ($coursesFitBasic as $c) {
            Predmet::create([
                'sifra_predmeta' => $c['Sifra Predmeta'] ?? '',
                'naziv' => $c['Naziv Predmeta'] ?? '',
                'naziv_engleski' => $c['Naziv Engleski'] ?? null,
                'ects' => (int) ($c['ECTS'] ?? 0),
                'semestar' => (int)$c['Semestar'],
                'fakultet_id' => $unimed->id,
                'nivo_studija_id' => $basic->id ?? null,
            ]);
        }

        foreach ($coursesFitMaster as $c) {
            Predmet::create([
                'sifra_predmeta' => $c['Sifra Predmeta'] ?? '',
                'naziv' => $c['Naziv Predmeta'] ?? '',
                'naziv_engleski' => $c['Naziv Engleski'] ?? null,
                'ects' => (int) ($c['ECTS'] ?? 0),
                'semestar' => (int)$c['Semestar'],
                'fakultet_id' => $unimed->id,
                'nivo_studija_id' => $master->id ?? null,
            ]);
        }
    }

    private function seedStraneFakultete(SubjectImportService $importer, ?NivoStudija $basic): void
    {
        $etfPath = storage_path('app/predmeti/etf_predmeti.xlsx');
        if (file_exists($etfPath)) {
            $coursesEtf = $importer->loadCoursesGeneric($etfPath);
            $etf = Fakultet::where('naziv', 'LIKE', '%ETF%')
                            ->orWhere('naziv', 'LIKE', '%Elektrotehnički%')
                            ->first();

            if ($etf) {
                foreach ($coursesEtf as $c) {
                    Predmet::create([
                        'sifra_predmeta' => $c['Sifra Predmeta'] ?? '',
                        'naziv' => $c['Naziv Predmeta'] ?? '',
                        'naziv_engleski' => null,
                        'ects' => (int) ($c['ECTS'] ?? 0),
                        'semestar' => (int)$c['Semestar'],
                        'fakultet_id' => $etf->id,
                        'nivo_studija_id' => $basic->id ?? null,
                    ]);
                }
            }
        }
    }
}
