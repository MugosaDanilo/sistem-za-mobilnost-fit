<?php

namespace Database\Seeders;

use App\Models\Fakultet;
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
       $filePath = storage_path('app/predmeti/nove npp osnovne.docx');

       $coursesFit = $importer->loadCoursesFromFit($filePath);

        $unimed = Fakultet::where('naziv', 'FIT')->first();
        $osnovne = \App\Models\NivoStudija::where('naziv', 'Osnovne')->first();

        foreach ($coursesFit as $c) {

            Predmet::create([
                'naziv' => $c['Naziv predmeta'] ?? '',
                'naziv_engleski' => $c['Naziv predmeta(Eng)'] ?? null,
                'ects' => (int) ($c['ECTS'] ?? 0),
                'semestar' => $importer->romanToInt($c['Semestar'] ?? ''),
                'fakultet_id' => $unimed->id,
                'nivo_studija_id' => $osnovne->id ?? null,
            ]);
        }

        $etf = Fakultet::where('naziv', 'ETF')->first();

        if ($etf) {
            $predmeti = [
                ['naziv' => 'Programiranje 1', 'ects' => 6, 'semestar' => 1],
                ['naziv' => 'Matematika 1', 'ects' => 5, 'semestar' => 1],
                ['naziv' => 'Fizika', 'ects' => 4, 'semestar' => 1],
                ['naziv' => 'Algoritmi i strukture podataka', 'ects' => 6, 'semestar' => 2],
                ['naziv' => 'Baze podataka', 'ects' => 5, 'semestar' => 2],
            ];

            foreach ($predmeti as $p) {
                Predmet::create([
                    'naziv' => $p['naziv'],
                    'ects' => $p['ects'],
                    'semestar' => $p['semestar'],
                    'fakultet_id' => $etf->id,
                    'nivo_studija_id' => $osnovne->id ?? null,
                ]);
            }
        }
    }
}
