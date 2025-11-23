<?php

namespace App\Http\Controllers;

use App\Models\Fakultet;
use App\Models\LearningAgreement;
use App\Models\Mobilnost;
use App\Models\Student;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\JcTable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class MobilityController extends Controller
{
    public function index()
    {
        $students = Student::orderBy('ime')->orderBy('prezime')->get();
        $fakulteti = Fakultet::with('predmeti')->orderBy('naziv')->get();
        return view('mobility.index', compact('students', 'fakulteti'));
    }
    

    public function save(Request $request)
    {
        $request->validate([
            'ime' => 'required|string',
            'prezime' => 'required|string',
            'fakultet_id' => 'required|exists:fakulteti,id',
            'student_id' => 'required|exists:studenti,id',
            'broj_indeksa' => 'required|string',
            'datum_pocetka' => 'required|date',
            'datum_kraja' => 'required|date|after:datum_pocetka',
            'links' => 'required|array|min:1',
            'courses' => 'array',
        ]);

        $fakultetId = $request->fakultet_id;
        $studentId = $request->student_id;
        $datumPocetka = $request->datum_pocetka;
        $datumKraja = $request->datum_kraja;
        $links = $request->input('links', []);
        $courses = $request->input('courses', []);

        $courseMap = [];
        foreach ($courses as $c) {
            $name = $c['Course'] ?? $c['Predmet'] ?? $c['name'] ?? null;
            if ($name) {
                $courseMap[trim($name)] = [
                    'Term' => $c['Term'] ?? '',
                    'ECTS' => $c['ECTS'] ?? '',
                ];
            }
        }

        // Create or update Mobilnost
        $mobilnost = Mobilnost::create([
            'student_id' => $studentId,
            'fakultet_id' => $fakultetId,
            'datum_pocetka' => $datumPocetka,
            'datum_kraja' => $datumKraja,
        ]);

        \Illuminate\Support\Facades\Log::info('Mobilnost created', ['id' => $mobilnost->id]);
        \Illuminate\Support\Facades\Log::info('Links payload', ['links' => $links]);

        foreach ($links as $fitSubjectName => $foreignSubjects) {
            $fitSubjectName = trim($fitSubjectName);
            \Illuminate\Support\Facades\Log::info('Processing FIT subject', ['name' => $fitSubjectName]);

            // Find FIT subject
            // Try exact match first
            $fitPredmet = \App\Models\Predmet::where('naziv', $fitSubjectName)
                ->whereHas('fakultet', function($q) {
                    $q->where('naziv', 'FIT');
                })->first();

            if (!$fitPredmet) {
                // Try ignoring whitespace
                $normalizedName = str_replace(' ', '', $fitSubjectName);
                $fitPredmet = \App\Models\Predmet::whereRaw("REPLACE(naziv, ' ', '') = ?", [$normalizedName])
                    ->whereHas('fakultet', function($q) {
                        $q->where('naziv', 'FIT');
                    })->first();
            }

            if (!$fitPredmet) {
                \Illuminate\Support\Facades\Log::warning('FIT subject not found', ['name' => $fitSubjectName]);
                // Fallback: try finding by name only (ignoring whitespace)
                 $fitPredmet = \App\Models\Predmet::whereRaw("REPLACE(naziv, ' ', '') = ?", [$normalizedName])->first();
                 if (!$fitPredmet) continue;
            }

            foreach ($foreignSubjects as $foreignSubjectName) {
                $foreignSubjectName = trim($foreignSubjectName);
                \Illuminate\Support\Facades\Log::info('Processing Foreign subject', ['name' => $foreignSubjectName]);

                // Find or create foreign subject
                $foreignPredmet = \App\Models\Predmet::firstOrCreate(
                    [
                        'naziv' => $foreignSubjectName,
                        'fakultet_id' => $fakultetId
                    ],
                    [
                        'ects' => $courseMap[$foreignSubjectName]['ECTS'] ?? 0,
                        'semestar' => 0 // Default or extract if possible
                    ]
                );
                
                if ($foreignPredmet->wasRecentlyCreated) {
                    \Illuminate\Support\Facades\Log::warning('Duplicate/New subject created!', [
                        'name' => $foreignSubjectName,
                        'fakultet_id' => $fakultetId,
                        'id' => $foreignPredmet->id
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::info('Existing subject found', ['id' => $foreignPredmet->id]);
                }

                LearningAgreement::create([
                    'mobilnost_id' => $mobilnost->id,
                    'fit_predmet_id' => $fitPredmet->id,
                    'strani_predmet_id' => $foreignPredmet->id,
                    'napomena' => null,
                    'ocjena' => null
                ]);
                
                \Illuminate\Support\Facades\Log::info('LA created');
            }
        }

        return response()->json(['message' => 'Learning Agreement saved successfully.']);
    }

    public function export(Request $request)
    {
        $request->validate([
            'links'     => 'required|array|min:1',
            'courses'   => 'array',
            'ime'       => 'required|string',
            'prezime'   => 'required|string',
            'fakultet'  => 'required|string',
            'brojIndeksa' => 'required|string'
        ]);

        $links = $request->input('links', []);
        $courses = $request->input('courses', []);
        $ime = trim($request->input('ime'));
        $prezime = trim($request->input('prezime'));
        $fakultet = trim($request->input('fakultet'));
        $brojIndeksa = trim($request->input('brojIndeksa'));

        $courseMap = [];
        foreach ($courses as $c) {
            $name = $c['Course'] ?? $c['Predmet'] ?? $c['name'] ?? null;
            if ($name) {
                $courseMap[trim($name)] = [
                    'Term' => $c['Term'] ?? '',
                    'ECTS' => $c['ECTS'] ?? '',
                ];
            }
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Mobilnost', ['bold' => true, 'size' => 16]);
        $section->addTextBreak(1);

        $textRun = $section->addTextRun(['size' => 12]);
        $textRun->addText('Student osnovnih studija ');
        $textRun->addText("{$ime} {$prezime} {$brojIndeksa}", ['bold' => true]);
        $textRun->addText(' će boraviti na ');
        $textRun->addText($fakultet, ['bold' => true]);
        $textRun->addText('.');

        $section->addText(
            'Studentu ce se priznavati sledeci ispiti:',
            ['size' => 12]
        );
        $section->addTextBreak(1);

        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER
        ];
        $phpWord->addTableStyle('CoursesTable', $tableStyle);
        $table = $section->addTable('CoursesTable');

        // Headeri
        $headers = ['R.br', 'Predmet (FIT)', 'Semestar', 'ECTS', 'Priznaje se', 'ECTS'];
        $table->addRow();
        foreach ($headers as $header) {
            $table->addCell(2000)->addText($header, ['bold' => true]);
        }

        $rowNum = 1;
        foreach ($links as $fitSubject => $linkedSubjects) {
            if (empty($linkedSubjects)) continue;

            $term = $courseMap[$fitSubject]['Term'] ?? '';
            $ects = $courseMap[$fitSubject]['ECTS'] ?? '';

            $table->addRow();
            $table->addCell(800)->addText($rowNum++);
            $table->addCell(3000)->addText($fitSubject);
            $table->addCell(1200)->addText($term);
            $table->addCell(800)->addText($ects);

            $textRun = $table->addCell(4000)->addTextRun();
            foreach ($linkedSubjects as $i => $subj) {
                $textRun->addText($subj);
                if ($i < count($linkedSubjects) - 1) {
                    $textRun->addTextBreak();
                }
            }

            $table->addCell(800)->addText('/');
        }

        $section->addTextBreak(2);
        $section->addText('Dekan,', ['bold' => true]);
        $section->addText('___________________________');

        $fileName = 'Mobilnost_' . date('Ymd_His') . '.docx';
        $filePath = storage_path("app/public/$fileName");

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'word_file' => 'required|mimes:doc,docx|max:10240',
        ]);

        $file = $request->file('word_file');
        $path = $file->storeAs('uploads', $file->getClientOriginalName());

        $courses = [];
        $this->loadCoursesWithoutGrades(storage_path('app/private/' . $path), $courses);

        return redirect()->route($this->getRedirectRoute())->with('courses', $courses)->withInput();
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
