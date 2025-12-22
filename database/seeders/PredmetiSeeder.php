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
        $coursesFit = [];
        $filePath = storage_path('app/predmeti/nove npp osnovne.docx');

        $this->loadCoursesFromFit($filePath, $coursesFit);

        $unimed = Fakultet::where('naziv', 'FIT')->first();
        $osnovne = \App\Models\NivoStudija::where('naziv', 'Osnovne')->first();

        foreach ($coursesFit as $c) {

            Predmet::create([
                'naziv' => $c['Naziv predmeta'] ?? '',
                'naziv_engleski' => $c['Naziv predmeta(Eng)'] ?? null,
                'ects' => (int) ($c['ECTS'] ?? 0),
                'semestar' => $this->romanToInt($c['Semestar'] ?? ''),
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


    function getElementText($element): string
    {
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

    function loadCoursesFromFit(string $filePath, array &$courses)
    {
        $phpWord = IOFactory::load($filePath);
        $lastIdxMap = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (!($element instanceof Table)) {
                    continue;
                }

                $rows = $element->getRows();
                if (count($rows) < 1) 
                    continue;

                $headerIndex = -1;
                $idxMap = [];
                $tableData = [];

                foreach ($rows as $rowIndex => $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $cellText = '';
                        foreach ($cell->getElements() as $cellElement) {
                            $cellText .= $this->getElementText($cellElement) . ' ';
                        }
                        $rowData[] = trim($cellText);
                    }

                 
                    if ($headerIndex === -1) {
                        $keys = array_map('mb_strtolower', $rowData);
                        $keys = array_map(function($k) { return trim(str_replace("\n", "", $k)); }, $keys);
                        
                        $nazivIdx = -1;
                        $ectsIdx = -1;
                        
                        foreach($rowData as $i => $colName) {
                            $cleanName = mb_strtolower(trim(str_replace(["\n", "\r"], "", $colName)));
                            if (str_contains($cleanName, 'naziv predmeta') && !str_contains($cleanName, '(eng)')) {
                                $nazivIdx = $i;
                            }
                            if (str_contains($cleanName, 'ects')) {
                                $ectsIdx = $i;
                            }
                        }

                        if ($nazivIdx !== -1 && $ectsIdx !== -1) {
                            $headerIndex = $rowIndex;
                            
                            foreach($rowData as $i => $colName) {
                                $cleanName = trim(str_replace(["\n", "\r"], "", $colName));
                                if (stripos($cleanName, 'Naziv predmeta') !== false && stripos($cleanName, '(Eng)') === false) {
                                    $idxMap['Naziv predmeta'] = $i;
                                } elseif (stripos($cleanName, 'Naziv predmeta(Eng)') !== false || stripos($cleanName, 'Naziv predmeta (Eng)') !== false) {
                                    $idxMap['Naziv predmeta(Eng)'] = $i;
                                } elseif (stripos($cleanName, 'Šifra') !== false || stripos($cleanName, 'Sifra') !== false) {
                                    $idxMap['Šifra predmeta'] = $i;
                                } elseif (stripos($cleanName, 'Status') !== false) {
                                    $idxMap['Status'] = $i;
                                } elseif (stripos($cleanName, 'Semestar') !== false) {
                                     $idxMap['Semestar'] = $i;
                                } elseif (stripos($cleanName, 'ECTS') !== false) {
                                    $idxMap['ECTS'] = $i;
                                }
                            }
                            $lastIdxMap = $idxMap;
                            continue; 
                        }
                    }

                    
                    $isHeaderRow = ($headerIndex !== -1 && $rowIndex === $headerIndex);
                    
                    if (!$isHeaderRow) {
                        if (stripos(implode(' ', $rowData), 'ukupno') !== false)
                            continue;

                        if (count(array_filter($rowData)) < 2) 
                            continue;

                        $tableData[] = $rowData;
                    }
                }

                $currentMap = !empty($idxMap) ? $idxMap : $lastIdxMap;

                if (empty($currentMap)) {
                     continue; 
                }

                foreach ($tableData as $r) {
                    if (isset($currentMap['Naziv predmeta']) && isset($r[$currentMap['Naziv predmeta']]) && !empty($r[$currentMap['Naziv predmeta']])) {
                        
                        $item = [];
                        foreach($currentMap as $key => $idx) {
                            $item[$key] = $r[$idx] ?? '';
                        }
                        
                        $courses[] = $item;
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
