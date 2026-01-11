<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use App\Models\Predmet;
use Illuminate\Database\Seeder;

use App\Services\SubjectImportService;

class PredmetiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $importer = new SubjectImportService();
        $fitPath = storage_path('app/predmeti/FIT_Nastavni_planovi_1.xlsx');

        $coursesFitBasic = $importer->loadCoursesFit($fitPath, 'basic');
        $coursesFitMaster = $importer->loadCoursesFit($fitPath, 'master');

        $unimed = Fakultet::where('naziv', 'LIKE', '%FIT%')
                        ->orWhere('naziv', 'LIKE', '%Fakultet za informacione tehnologije%')
                        ->first();
        
        $basic = NivoStudija::where('naziv', 'Osnovne')->first();
        $master = NivoStudija::where('naziv', 'Master')->first();

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
