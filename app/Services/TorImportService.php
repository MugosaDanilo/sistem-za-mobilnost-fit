<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

class TorImportService
{
    /**
     * Import Tor file.
     *
     * @param string $filePath
     * @return void
     */
    public function import(string $filePath)
    {
        return $this->loadCoursesWithGrades($filePath);
    }

    public function loadCoursesWithGrades(string $filePath): array
    {
        $courses = [];
        $phpWord = IOFactory::load($filePath);

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (!($element instanceof Table)) continue;

                $rows = $element->getRows();
                $headerMap = [];
                $headerRowFound = false;

                foreach ($rows as $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $cellText = '';
                        foreach ($cell->getElements() as $cellElement) {
                            $cellText .= $this->getElementText($cellElement) . ' ';
                        }
                        $rowData[] = trim(preg_replace('/\s+/', ' ', $cellText));
                    }

                    $rowData = array_values(array_filter($rowData, fn($v) => $v !== ''));

                    if (!$headerRowFound) {
                        $normalized = array_map(fn($v) => strtolower(trim($v)), $rowData);
                        
                        // Check for at least 2 keywords to confirm it's a header row
                        if (count(array_intersect($normalized, ['term', 'semester', 'course', 'subject', 'title', 'grade', 'ects', 'credits', 'points'])) >= 2) {
                            foreach ($normalized as $i => $header) {
                                $headerMap[$header][] = $i;
                            }
                            $headerRowFound = true;
                        }
                        continue;
                    }

                    if (!$headerRowFound) continue;

                    $gradeCandidates = $headerMap['grade'] ?? [];
                    $gradeLetter = null;
                    foreach ($gradeCandidates as $colIdx) {
                        $value = $rowData[$colIdx] ?? null;
                        if ($value && preg_match('/^[A-F][+-]?$/i', trim($value))) {
                            $gradeLetter = strtoupper(trim($value));
                            break;
                        }
                    }

                    $term = $this->getColumnValue($rowData, $headerMap, ['term', 'semester']);
                    $course = $this->getColumnValue($rowData, $headerMap, ['course', 'subject', 'title']);
                    $ects = $this->getColumnValue($rowData, $headerMap, ['ects', 'credits', 'points']);

                    if ($course && $gradeLetter) {
                        $courses[] = [
                            'Term' => $term,
                            'Course' => $course,
                            'Grade' => $gradeLetter,
                            'ECTS' => $ects,
                        ];
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

    private function getColumnValue(array $rowData, array $headerMap, array $possibleNames): ?string
    {
        foreach ($possibleNames as $name) {
            if (isset($headerMap[$name])) {
                $idxs = (array)$headerMap[$name];
                foreach ($idxs as $i) {
                    if (!empty($rowData[$i])) return $rowData[$i];
                }
            }
        }
        return null;
    }
}
