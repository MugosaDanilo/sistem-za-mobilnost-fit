<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class MobilityController extends Controller
{
    public function index()
    {
        return view('mobility.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'word_file' => 'required|mimes:doc,docx|max:10240',
        ]);

        $file = $request->file('word_file');
        $path = $file->storeAs('uploads', $file->getClientOriginalName());
        Log::info('File stored at: ' . storage_path('app/' . $path));


        $courses = [];
        $this->loadCoursesWithoutGrades(storage_path('app/private/' . $path), $courses);

        return redirect()->route($this->getRedirectRoute())->with('courses', $courses);
    }

    private function getRedirectRoute(): string
    {
        $user = Auth::user();
        if ((int)$user->type === 0) {
            return 'admin.mobility';
        }
        return 'profesor.mobility';
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

    private function loadCoursesWithoutGrades(string $filePath, array &$courses)
    {
        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

        if (!file_exists($filePath)) {
            throw new \Exception("File not found at path: $filePath");
        }

        try {
            $phpWord = IOFactory::load($filePath);
        } catch (\Exception $e) {
            Log::error('PhpWord failed to load file: ' . $filePath . ' | ' . $e->getMessage());
            throw $e;
        }

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (!($element instanceof Table)) continue;

                $rows = $element->getRows();
                $headerMap = [];
                $headerRowFound = false;
                $headerRowValues = [];

                foreach ($rows as $row) {
                    $rowData = [];
                    foreach ($row->getCells() as $cell) {
                        $cellText = '';
                        foreach ($cell->getElements() as $cellElement) {
                            $cellText .= $this->getElementText($cellElement) . ' ';
                        }
                        $rowData[] = trim(preg_replace('/\s+/', ' ', $cellText));
                    }

                    $rowData = array_map(fn($v) => trim($v), $rowData);

                    if (!$headerRowFound) {
                        $normalized = array_map(fn($v) => strtolower(trim($v)), $rowData);
                        if (count(array_intersect($normalized, ['term', 'semester', 'course', 'subject', 'title', 'grade', 'ects', 'credits', 'points'])) >= 2) {
                            foreach ($normalized as $i => $header) {
                                $headerMap[$header][] = $i;
                            }
                            $headerRowFound = true;
                            $headerRowValues = $rowData;
                        }
                        continue;
                    }

                    if ($rowData === $headerRowValues) {
                        continue;
                    }

                    $gradeCandidates = $headerMap['grade'] ?? [];
                    $gradeLetter = null;
                    foreach ($gradeCandidates as $colIdx) {
                        $value = $rowData[$colIdx] ?? null;
                        if ($value && preg_match('/^[A-F][+-]?$/i', trim($value))) {
                            $gradeLetter = strtoupper(trim($value));
                            break;
                        }
                    }

                    if ($gradeLetter) {
                        continue;
                    }

                    $term = $this->getColumnValue($rowData, $headerMap, ['term', 'semester']);
                    $course = $this->getColumnValue($rowData, $headerMap, ['course', 'subject', 'title']);
                    $ects = $this->getColumnValue($rowData, $headerMap, ['ects', 'credits', 'points']);

                    if ($course) {
                        $courses[] = [
                            'Term' => $term,
                            'Course' => $course,
                            'ECTS' => $ects, 
                        ];
                    }
                }
            }
        }
    }
}
