<?php

namespace App\Services;

use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SubjectImportService
{
    private const STUDY_SHEETS = [
        'basic' => ['OSNOVNE STUDIJE', 'Undergraduate'],
        'master' => ['MASTER STUDIJE', "Master's"],
    ];
    
    public function loadCoursesFit(string $filePath, string $level = 'basic') {
        if (!isset(self::STUDY_SHEETS[$level])) {
            throw new InvalidArgumentException("Invalid study level: {$level}");
        }

        [$nativeSheetName, $englishSheetName] = self::STUDY_SHEETS[$level];

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly([$nativeSheetName, $englishSheetName]);

        $spreadsheet = $reader->load($filePath);

        $nativeSheet = $spreadsheet->getSheetByName($nativeSheetName);
        $englishSheet = $spreadsheet->getSheetByName($englishSheetName);

        $nativeRows = $nativeSheet->rangeToArray(
            'A1:' . $nativeSheet->getHighestDataColumn() . $nativeSheet->getHighestDataRow(),
            null,
            true,
            true,
            false
        );

        $englishRows = $englishSheet->rangeToArray(
            'A1:' . $englishSheet->getHighestDataColumn() . $englishSheet->getHighestDataRow(),
            null,
            true,
            true,
            false
        );

        $englishHeadersMap = $this->findHeaderRow($englishRows, ['Course']);
        
        if (!$englishHeadersMap) {
             throw new \Exception("Could not find 'Course' header in '{$englishSheetName}' sheet.");
        }
        
        $englishCourseIndex = $englishHeadersMap['Course'];

        $codeHeaderCandidates = ['Code', 'Šifra', 'Šifra predmeta', 'Sifra predmeta', 'Sifra'];
        $englishCodeIndex = null;
        
        $englishHeaderRowIndex = $englishHeadersMap['_rowIndex'];
        $englishHeaderRow = $englishRows[$englishHeaderRowIndex];

        foreach ($englishHeaderRow as $index => $cellValue) {
             $cellValue = trim($cellValue ?? '');
             if (in_array($cellValue, $codeHeaderCandidates)) {
                 $englishCodeIndex = $index;
                 break;
             }
        }

        if ($englishCodeIndex === null) {
             $englishCodeIndex = ($level === 'basic') ? 1 : 2; 
        }

        $englishMap = [];
        foreach ($englishRows as $rowIndex => $row) {
            if ($rowIndex <= $englishHeaderRowIndex) continue; // Skip headers
            if (!array_filter($row)) continue;

            $code = trim($row[$englishCodeIndex] ?? '');
            if ($code !== '') {
                $englishMap[$code] = $row[$englishCourseIndex] ?? null;
            }
        }

        $requiredNativeHeaders = ['Šifra predmeta', 'Naziv predmeta', 'Semestar', 'ECTS'];
        $nativeHeadersMap = $this->findHeaderRow($nativeRows, $requiredNativeHeaders);
        
        if (!$nativeHeadersMap) {
             throw new \Exception("Could not find required headers (" . implode(', ', $requiredNativeHeaders) . ") in '{$nativeSheetName}' sheet.");
        }

        $nativeHeaderRowIndex = $nativeHeadersMap['_rowIndex'];

        $courses = [];
        foreach ($nativeRows as $rowIndex => $row) {
            if ($rowIndex <= $nativeHeaderRowIndex) continue;
            if (!array_filter($row)) continue;

            $sifraVal = trim($row[$nativeHeadersMap['Šifra predmeta']] ?? '');
            $nazivVal = trim($row[$nativeHeadersMap['Naziv predmeta']] ?? '');
            
            if ($sifraVal === '' || $nazivVal === '') continue;

            if ($sifraVal === 'Šifra predmeta' || $nazivVal === 'Naziv predmeta') continue;

            $courses[] = [
                'Sifra Predmeta' => $sifraVal,
                'Naziv Predmeta' => $nazivVal,
                'Naziv Engleski' => $englishMap[$sifraVal] ?? null,
                'Semestar'       => $this->romanToInt($row[$nativeHeadersMap['Semestar']] ?? ''),
                'ECTS'           => $row[$nativeHeadersMap['ECTS']] ?? null,
            ];
        }

        return $courses;
    }

    private function findHeaderRow(array $rows, array $requiredHeaders): ?array {
        $maxSearch = 20;
        
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex >= $maxSearch) break;
            
            $map = [];
            $foundCount = 0;
            
            foreach ($row as $colIndex => $cellValue) {
                $cellValue = trim((string)$cellValue);
                if (in_array($cellValue, $requiredHeaders)) {
                    $map[$cellValue] = $colIndex;
                    $foundCount++;
                }
            }

            if ($foundCount === count($requiredHeaders)) {
                $map['_rowIndex'] = $rowIndex;
                return $map;
            }
        }

        return null;
    }

   
    private function romanToInt(?string $roman): int {
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
