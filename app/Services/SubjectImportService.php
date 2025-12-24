<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

class SubjectImportService
{
    public function loadCoursesFromFit(string $filePath): array
    {
        $courses = [];
        $phpWord = IOFactory::load($filePath);
        $lastIdxMap = [];
        $lastHeaderCount = 0;

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
                $headerCount = 0;

                foreach ($rows as $rowIndex => $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $cellText = '';
                        foreach ($cell->getElements() as $cellElement) {
                            $cellText .= $this->getElementText($cellElement) . ' ';
                        }
                        $rowData[] = trim($cellText);
                    }

                    // Header Detection Logic
                    if ($headerIndex === -1) {
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
                            $headerCount = count($rowData);
                            
                        foreach($rowData as $i => $colName) {
                            // Better cleaning: replace newlines with spaces, then collapse multiple spaces
                            $cleanName = trim(preg_replace('/\s+/', ' ', str_replace(["\n", "\r"], " ", $colName)));
                            
                            $cleanLower = mb_strtolower($cleanName);

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
                            } elseif (stripos($cleanName, 'casova') !== false || stripos($cleanName, 'sati') !== false || stripos($cleanName, 'hours') !== false) {
                                 $idxMap['SplitColumn'] = $i;
                            }
                        }
                            $lastIdxMap = $idxMap;
                            $lastHeaderCount = $headerCount;
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
                $currentHeaderCount = !empty($idxMap) ? $headerCount : $lastHeaderCount;
                
                if (empty($currentMap)) {
                     continue; 
                }

                $splitColIdx = $currentMap['SplitColumn'] ?? -1;

                foreach ($tableData as $r) {
                    if (isset($currentMap['Naziv predmeta']) && isset($r[$currentMap['Naziv predmeta']]) && !empty($r[$currentMap['Naziv predmeta']])) {
                        
                        $item = [];
                        $shift = 0;
                        if ($currentHeaderCount > 0 && count($r) > $currentHeaderCount) {
                            $shift = count($r) - $currentHeaderCount;
                        }

                        foreach($currentMap as $key => $idx) {
                            if ($key === 'SplitColumn') continue;

                            $targetIdx = $idx;
                            
                            // Robust Logic
                            if ($shift > 0) {
                                if ($splitColIdx !== -1) {
                                    if ($idx > $splitColIdx) {
                                        $targetIdx += $shift;
                                    }
                                } elseif ($key === 'ECTS') {
                                    $targetIdx += $shift; 
                                }
                            }

                            $item[$key] = $r[$targetIdx] ?? '';
                        }
                        
                        $courses[] = $item;
                    }
                }
            }
        }
        return $courses;
    }

    private function getElementText($element): string
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

    public function romanToInt(?string $roman): int
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
