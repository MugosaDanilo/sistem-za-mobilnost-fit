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

class PredmetiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coursesFit = [];
        $filePath = storage_path('app/predmeti/1. NPP-osnovne.docx');

        $this->loadCoursesFromFit($filePath, $coursesFit);

        $unimed = Fakultet::where('naziv', 'FIT')->first();

        foreach ($coursesFit as $c) {

            Predmet::create([
                'naziv'    => $c['Naziv predmeta'] ?? '',
                'ects'     => $c['ECTS'] ?? 0,
                'semestar' => $this->romanToInt($c['Semestar'] ?? ''),
                'fakultet_id' => $unimed->id,
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
                ]);
            }
        }
    }


    function getElementText($element): string {
        $text = '';

        if ($element instanceof Text) {
            $text .= $element->getText();
        } elseif ($element instanceof TextRun) {
            foreach ($element->getElements() as $child) {
                $text .= $this->getElementText($child) . ' ';
            }
        }
        return trim($text);
    }

    function loadCoursesFromFit(string $filePath, array &$courses) {
        $phpWord = IOFactory::load($filePath);

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (!($element instanceof Table)) {
                    continue;
                }

                $rows = $element->getRows();
                if (count($rows) < 3) continue;

                $tableData = [];
                foreach ($rows as $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $cellText = '';
                        foreach ($cell->getElements() as $cellElement) {
                            $cellText .= $this->getElementText($cellElement) . ' ';
                        }
                        $rowData[] = trim($cellText);
                    }

                    // preskoci sumarne redove "ukupno"
                    if (stripos(implode(' ', $rowData), 'ukupno') !== false) continue;

                    $tableData[] = $rowData;
                }

                foreach ($tableData as $r) {
                    if (!empty($r[0]) && !empty($r[1]) && is_numeric(end($r))) {
                        $courses[] = [
                            "Šifra predmeta" => $r[0] ?? '',
                            "Naziv predmeta" => $r[1] ?? '',
                            "Status"         => $r[2] ?? '',
                            "Semestar"       => $r[3] ?? 0,
                            "P"              => $r[4] ?? 0,
                            "V"              => $r[5] ?? 0,
                            "L"              => $r[6] ?? 0,
                            "ECTS"           => $r[7] ?? 0,
                        ];
                    }
                }
            }
        }
    }

    function romanToInt(?string $roman): int
    {
        $map = [
            'I' => 1,
            'II' => 2,
            'III' => 3,
            'IV' => 4,
            'V' => 5,
            'VI' => 6,
            'VII' => 7,
            'VIII' => 8,
            'IX' => 9,
            'X' => 10,
        ];

        $roman = trim($roman ?? '');

        return $map[$roman] ?? 0;
    }

}
