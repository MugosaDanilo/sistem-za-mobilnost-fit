<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Predmet;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    public function importTor(Request $request)
    {
        $request->validate([
            'tor_file' => 'required|file|mimes:docx',
        ]);

        $file = $request->file('tor_file');
        Log::info("Starting import for file: " . $file->getPathname());

        try {
            $phpWord = IOFactory::load($file->getPathname());
        } catch (\Exception $e) {
            Log::error("Failed to load Word file: " . $e->getMessage());
            return response()->json(['error' => 'Failed to parse file'], 500);
        }

        $importedSubjects = [];
        $allSubjects = Predmet::all(['id', 'naziv', 'ects', 'semestar', 'naziv_engleski'])
            ->mapWithKeys(function ($item) {
                return [strtolower(trim($item->naziv)) => $item];
            });

        Log::info("Loaded " . $allSubjects->count() . " subjects from DB.");

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $this->processTable($element, $importedSubjects, $allSubjects);
                }
            }
        }

        Log::info("Import complete. Found match count: " . count($importedSubjects));
        return response()->json($importedSubjects);
    }

    private function processTable($table, &$importedSubjects, $allSubjects)
    {
        $rows = $table->getRows();
        $rowCount = count($rows);

        if ($rowCount < 2)
            return;

        $headerRowIndex = -1;
        $courseIdx = -1;
        $gradeIdx = -1;
        $ectsIdx = -1;

        for ($r = 0; $r < $rowCount; $r++) {
            $cells = $rows[$r]->getCells();
            $texts = [];
            foreach ($cells as $cell) {
                $texts[] = strtolower(trim($this->getCellText($cell)));
            }

            $rowString = implode(' ', $texts);

            // Very lenient detection
            $hasCourse = str_contains($rowString, 'course') || str_contains($rowString, 'predmet') || str_contains($rowString, 'subject');
            $hasGrade = str_contains($rowString, 'grade') || str_contains($rowString, 'ocjena') || str_contains($rowString, 'mark');
            $hasEcts = str_contains($rowString, 'ects') || str_contains($rowString, 'credits');

            if ($hasCourse && ($hasGrade || $hasEcts)) {
                foreach ($texts as $index => $txt) {
                    if ($courseIdx === -1 && (str_contains($txt, 'course') || str_contains($txt, 'predmet') || str_contains($txt, 'subject')))
                        $courseIdx = $index;
                    if ($gradeIdx === -1 && (str_contains($txt, 'grade') || str_contains($txt, 'ocjena') || str_contains($txt, 'mark')))
                        $gradeIdx = $index;
                    if ($ectsIdx === -1 && (str_contains($txt, 'ects') || str_contains($txt, 'credit')))
                        $ectsIdx = $index;
                }

                if ($courseIdx !== -1) {
                    $headerRowIndex = $r;
                    Log::info("Header found at Row $r. Indices: Course=$courseIdx, Grade=$gradeIdx, ECTS=$ectsIdx");
                    break;
                }
            }
        }

        if ($headerRowIndex === -1) {
            Log::warning("No table header found in Word document.");
            return;
        }

        // 1. Build a comprehensive map for faster matching
        $subjectMap = [];
        foreach ($allSubjects as $subj) {
            $subjectMap[strtolower(trim($subj->naziv))] = $subj;
            if ($subj->naziv_engleski) {
                $subjectMap[strtolower(trim($subj->naziv_engleski))] = $subj;
            }
        }

        for ($i = $headerRowIndex + 1; $i < $rowCount; $i++) {
            try {
                $cells = $rows[$i]->getCells();
                if (!isset($cells[$courseIdx]))
                    continue;

                $courseName = trim($this->getCellText($cells[$courseIdx]));
                if (empty($courseName) || strlen($courseName) < 3)
                    continue;

                // Skip if this row is another header row matching our search
                $lowerCourse = strtolower($courseName);
                if ($lowerCourse === 'course' || $lowerCourse === 'predmet' || $lowerCourse === 'subject') {
                    continue;
                }

                $grade = ($gradeIdx !== -1 && isset($cells[$gradeIdx])) ? trim($this->getCellText($cells[$gradeIdx])) : '';
                $numericGrade = $this->mapGrade($grade);

                // MATCHING
                $dbSubject = $this->findSubjectRobustly($courseName, $subjectMap, $allSubjects);

                if ($dbSubject) {
                    // Avoid duplicates in the same import session
                    $exists = false;
                    foreach ($importedSubjects as $existing) {
                        if ($existing['id'] == $dbSubject->id) {
                            $exists = true;
                            break;
                        }
                    }
                    if ($exists)
                        continue;

                    Log::info("Matched: " . $courseName . " to ID " . $dbSubject->id . " (" . $dbSubject->naziv . ")");
                    $importedSubjects[] = [
                        'id' => $dbSubject->id,
                        'naziv' => $dbSubject->naziv,
                        'semestar' => $dbSubject->semestar,
                        'ects' => $dbSubject->ects,
                        'grade' => $numericGrade ?? '',
                        'original_grade' => $grade
                    ];
                } else {
                    Log::warning("No match for course: " . $courseName);
                }
            } catch (\Exception $e) {
                Log::error("Error processing row $i: " . $e->getMessage());
            }
        }
    }

    private function findSubjectRobustly($name, $map, $all)
    {
        $name = strtolower(trim($name));
        $cleanDoc = $this->cleanForMatching($name);

        if (isset($map[$name]))
            return $map[$name];
        if (isset($map[$cleanDoc]))
            return $map[$cleanDoc];

        $docLevel = null;
        if (preg_match('/\b(1|2|3|4|5|6)\b/', $cleanDoc, $matches)) {
            $docLevel = $matches[1];
        }

        $docWords = explode(' ', $cleanDoc);
        $docStems = array_map(fn($w) => (strlen($w) > 4) ? substr($w, 0, 5) : $w, $docWords);

        $bestMatch = null;
        $bestScore = -1;

        foreach ($all as $subj) {
            foreach ([$subj->naziv, $subj->naziv_engleski ?: ''] as $origPn) {
                if (empty($origPn))
                    continue;
                $pn = $this->cleanForMatching($origPn);

                // STRIKT LEVEL CHECK
                $subjLevel = null;
                if (preg_match('/\b(1|2|3|4|5|6)\b/', $pn, $matches)) {
                    $subjLevel = $matches[1];
                }

                if (($docLevel !== null || $subjLevel !== null) && ($docLevel !== $subjLevel)) {
                    continue;
                }

                $pnWords = explode(' ', $pn);
                $pnStems = array_map(fn($w) => (strlen($w) > 4) ? substr($w, 0, 5) : $w, $pnWords);

                $intersect = array_intersect($docStems, $pnStems);
                // DENOMINATOR: Max of both word counts to penalize partial matches on long names
                $score = count($intersect) / max(count($docStems), count($pnStems), 1);

                // Tie-breaker: prefer matches that start with the same word
                if (isset($docStems[0]) && isset($pnStems[0]) && $docStems[0] === $pnStems[0]) {
                    $score += 0.05;
                }

                // Bonus for exact length match
                if (count($docStems) === count($pnStems)) {
                    $score += 0.05;
                }

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $subj;
                }
            }
        }

        return ($bestScore >= 0.4) ? $bestMatch : null;
    }

    private function cleanForMatching($string)
    {
        if (!$string)
            return '';
        $string = strtolower($string);

        // Roman numerals (only whole words)
        $romanMap = ['iii' => '3', 'ii' => '2', 'i' => '1', 'iv' => '4', 'v' => '5', 'vi' => '6'];
        foreach ($romanMap as $r => $n) {
            $string = preg_replace('/\b' . $r . '\b/', $n, $string);
        }

        $replace = [
            'basics' => 'osnov',
            'fundamentals' => 'osnov',
            'osnovne' => 'osnov',
            'osnovi' => 'osnov',
            'programming' => 'programiranje',
            'design' => 'dizajn',
            'projektovanje' => 'dizajn',
            'mathematics' => 'matematika',
            'matemathics' => 'matematika',
            'tecnologies' => 'tehnologije',
            'technologies' => 'tehnologije',
            'english' => 'engleski',
            'security' => 'sigurnost',
            'protection' => 'zaštita',
            'business' => 'poslovni',
            'information' => 'informacion',
            'systems' => 'sistem',
            'operating' => 'operativni',
            'networks' => 'mrežne',
            'network' => 'mrežne',
        ];
        foreach ($replace as $f => $t) {
            $string = str_replace($f, $t, $string);
        }

        // Filter words (only whole words)
        $filters = ['language', 'for', 'the', 'of', 'in', 'and', '&'];
        foreach ($filters as $filter) {
            $string = preg_replace('/\b' . $filter . '\b/', ' ', $string);
        }

        $string = str_replace([',', '.', '(', ')'], ' ', $string);
        return preg_replace('/\s+/', ' ', trim($string));
    }

    private function getCellText($element)
    {
        if ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            return " ";
        }

        $text = '';

        if (method_exists($element, 'getElements')) {
            $children = $element->getElements();
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $text .= $this->getCellText($child);
                }
                return $this->deduplicate($text);
            }
        }

        if (method_exists($element, 'getText')) {
            $text .= $element->getText();
        }

        return $text;
    }

    private function deduplicate($text)
    {
        $len = strlen($text);
        if ($len == 0)
            return $text;
        if ($len % 2 !== 0)
            return $text;

        $half = $len / 2;
        if (substr($text, 0, $half) === substr($text, $half)) {
            return substr($text, 0, $half);
        }
        return $text;
    }

    private function mapGrade($gradeStr)
    {
        if (empty($gradeStr))
            return null;
        $g = strtoupper(substr($gradeStr, 0, 1));
        return match ($g) {
            'A' => 10, 'B' => 9, 'C' => 8, 'D' => 7, 'E' => 6, default => null,
        };
    }
}
