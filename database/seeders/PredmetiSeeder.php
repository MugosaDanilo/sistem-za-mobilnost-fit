<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use App\Models\Predmet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

use App\Services\SubjectImportService;

class PredmetiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $importer = new SubjectImportService();
        $filePath = storage_path('app/predmeti/FIT_Nastavni_planovi_1.xlsx');

        $coursesFitBasic = $importer->loadCoursesFit($filePath, 'basic');
        $coursesFitMaster = $importer->loadCoursesFit($filePath, 'master');

        $unimed = Fakultet::where('naziv', 'FIT')->first();
        $basic = NivoStudija::where('naziv', 'Osnovne')->first();
        $master = NivoStudija::where('naziv', 'Master')->first();

        //Predmeti za osnovne
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
}
