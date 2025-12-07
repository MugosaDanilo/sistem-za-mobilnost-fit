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

            $fitPredmet = \App\Models\Predmet::where('naziv', $fitSubjectName)
                ->whereHas('fakultet', function ($q) {
                    $q->where('naziv', 'FIT');
                })->first();

            if (!$fitPredmet) {
                $normalizedName = str_replace(' ', '', $fitSubjectName);
                $fitPredmet = \App\Models\Predmet::whereRaw("REPLACE(naziv, ' ', '') = ?", [$normalizedName])
                    ->whereHas('fakultet', function ($q) {
                        $q->where('naziv', 'FIT');
                    })->first();
            }

            if (!$fitPredmet) {
                \Illuminate\Support\Facades\Log::warning('FIT subject not found', ['name' => $fitSubjectName]);
                $fitPredmet = \App\Models\Predmet::whereRaw("REPLACE(naziv, ' ', '') = ?", [$normalizedName])->first();
                
                if (!$fitPredmet) {
                    \Illuminate\Support\Facades\Log::info('Creating new FIT subject', ['name' => $fitSubjectName]);
                    $fitFaculty = \App\Models\Fakultet::where('naziv', 'FIT')->first();
                    
                    if ($fitFaculty) {
                        $fitPredmet = \App\Models\Predmet::create([
                            'naziv' => $fitSubjectName,
                            'fakultet_id' => $fitFaculty->id,
                            'ects' => $courseMap[$fitSubjectName]['ECTS'] ?? 0,
                            'semestar' => $courseMap[$fitSubjectName]['Term'] ?? 0 
                        ]);
                    }
                }

                if (!$fitPredmet)
                    continue;
            }

            foreach ($foreignSubjects as $foreignSubjectName) {
                $foreignSubjectName = trim($foreignSubjectName);
                \Illuminate\Support\Facades\Log::info('Processing Foreign subject', ['name' => $foreignSubjectName]);

                $foreignPredmet = \App\Models\Predmet::firstOrCreate(
                    [
                        'naziv' => $foreignSubjectName,
                        'fakultet_id' => $fakultetId
                    ],
                    [
                        'ects' => $courseMap[$foreignSubjectName]['ECTS'] ?? 0,
                        'semestar' => 0
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
            'links' => 'required|array|min:1',
            'courses' => 'array',
            'ime' => 'required|string',
            'prezime' => 'required|string',
            'fakultet' => 'required|string',
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

        $allForeignSubjects = [];
        foreach ($links as $linked) {
            $allForeignSubjects = array_merge($allForeignSubjects, $linked);
        }
        $allForeignSubjects = array_unique($allForeignSubjects);

        $fakultetModel = \App\Models\Fakultet::where('naziv', $fakultet)->first();
        $foreignSubjectEcts = collect();
        
        if ($fakultetModel) {
            $foreignSubjectEcts = \App\Models\Predmet::whereIn('naziv', $allForeignSubjects)
                ->where('fakultet_id', $fakultetModel->id)
                ->pluck('ects', 'naziv');
        }

        $rowNum = 1;
        // Fetch details for all FIT subjects involved
        $allFitSubjects = array_keys($links);
        $fitSubjectsDetails = \App\Models\Predmet::whereIn('naziv', $allFitSubjects)
            ->whereHas('fakultet', function ($q) {
                $q->where('naziv', 'FIT');
            })
            ->get()
            ->keyBy('naziv');

        foreach ($links as $fitSubject => $linkedSubjects) {
            if (empty($linkedSubjects))
                continue;

            $fitSubjectModel = $fitSubjectsDetails[$fitSubject] ?? null;
            $term = $fitSubjectModel ? $fitSubjectModel->semestar : '';
            $ects = $fitSubjectModel ? $fitSubjectModel->ects : '';

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

            $totalEcts = 0;
            foreach ($linkedSubjects as $subj) {
                $subj = trim($subj);
                $totalEcts += $foreignSubjectEcts[$subj] ?? 0;
            }

            $table->addCell(800)->addText($totalEcts);
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
        $this->findMissingFitSubjects(storage_path('app/private/' . $path), $courses);

        return redirect()->route($this->getRedirectRoute())->with('courses', $courses)->withInput();
    }

    private function getRedirectRoute(): string
    {
        $user = Auth::user();
        if ((int) $user->type === 0) {
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
                $idxs = (array) $headerMap[$name];
                foreach ($idxs as $i) {
                    if (!empty($rowData[$i]))
                        return $rowData[$i];
                }
            }
        }
        return null;
    }

    private function findMissingFitSubjects(string $filePath, array &$missingSubjects)
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

        $foundSubjectNames = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (!($element instanceof Table))
                    continue;

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
                    $gradeFound = false;
                    foreach ($gradeCandidates as $colIdx) {
                        $value = $rowData[$colIdx] ?? null;
                        if ($value && preg_match('/^[A-F][+-]?$/i', trim($value))) {
                            $gradeFound = true;
                            break;
                        }
                    }

                    // If NOT grade found, we skip (we only want passed subjects from ToR)
                    if (!$gradeFound) {
                        continue;
                    }

                    $course = $this->getColumnValue($rowData, $headerMap, ['course', 'subject', 'title']);
                    if ($course) {
                        $foundSubjectNames[] = $course;
                    }
                }
            }
        }

        // Fetch all FIT subjects
        $fitSubjects = \App\Models\Predmet::whereHas('fakultet', function ($q) {
            $q->where('naziv', 'FIT');
        })->get();

        $foundNormalized = array_map(fn($n) => strtolower(str_replace(' ', '', trim($n))), $foundSubjectNames);

        foreach ($fitSubjects as $fitData) {
            $fitNameNorm = strtolower(str_replace(' ', '', trim($fitData->naziv)));
            if (!in_array($fitNameNorm, $foundNormalized)) {
                $missingSubjects[] = $fitData->naziv;
            }
        }
    }
    public function show($id)
    {
        $mobilnost = Mobilnost::with(['student', 'fakultet', 'learningAgreements.fitPredmet', 'learningAgreements.straniPredmet'])->findOrFail($id);
        return view('mobility.show', compact('mobilnost'));
    }

    public function updateGrade(Request $request, $id)
    {
        $request->validate([
            'ocjena' => 'nullable|string|max:10',
        ]);

        $la = LearningAgreement::findOrFail($id);
        $la->update(['ocjena' => $request->ocjena]);

        return response()->json(['message' => 'Ocjena uspješno ažurirana.']);
    }

    public function updateGrades(Request $request, $id)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*' => 'nullable|string|max:10',
        ]);

        $mobilnost = Mobilnost::findOrFail($id);

        foreach ($request->grades as $laId => $grade) {
            $la = LearningAgreement::where('mobilnost_id', $mobilnost->id)->where('id', $laId)->first();
            if ($la) {
                $la->update(['ocjena' => $grade]);
            }
        }

        return response()->json(['message' => 'Grades updated successfully.']);
    }
    public function exportWord($id)
    {
        $mobilnost = Mobilnost::with(['student', 'fakultet', 'learningAgreements.fitPredmet', 'learningAgreements.straniPredmet'])
            ->findOrFail($id);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        $section->addText('Predlog za priznavanje ispita', ['bold' => true, 'size' => 16], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);

        $textRun = $section->addTextRun(['size' => 12]);
        $textRun->addText('Student osnovnih studija ');
        $textRun->addText("{$mobilnost->student->ime} {$mobilnost->student->prezime}", ['bold' => true]);
        $textRun->addText(", broj indeksa ");
        $textRun->addText($mobilnost->student->br_indexa, ['bold' => true]);
        $textRun->addText(", boravio je na ");
        $textRun->addText($mobilnost->fakultet->naziv, ['bold' => true]);
        $textRun->addText(". Na osnovu sporazuma o mobilnosti i transkripta ocjena, studentu treba da se priznaju sledeći ispiti:");
        $section->addTextBreak(1);

        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 80,
            'alignment' => JcTable::CENTER,
            'bgColor' => 'FFFFCC'
        ];
        $phpWord->addTableStyle('GradesTable', $tableStyle);
        $table = $section->addTable('GradesTable');

        $headers = ['R.br', 'Predmet (FIT)', 'Semestar', 'ECTS (FIT)', 'Priznaje se', 'Ocjena', 'ECTS'];
        $table->addRow();
        foreach ($headers as $header) {
            $table->addCell(2000, ['bgColor' => 'FFFFFF'])->addText($header, ['name' => 'Times New Roman', 'bold' => true, 'italic' => true]);
        }

        $groupedAgreements = $mobilnost->learningAgreements->groupBy('fit_predmet_id');

        $rowNum = 1;
        foreach ($groupedAgreements as $fitPredmetId => $agreements) {
            $fitPredmet = $agreements->first()->fitPredmet;
            
            if (!$fitPredmet) continue;

            $fitSubjectName = $fitPredmet->naziv;
            $semester = $fitPredmet->semestar;
            $fitEcts = $fitPredmet->ects;

            $foreignSubjects = [];
            $totalForeignEcts = 0;
            
            $gradeSum = 0;
            $gradeCount = 0;
            $gradeMap = ['A' => 10, 'B' => 9, 'C' => 8, 'D' => 7, 'E' => 6];
            
            foreach ($agreements as $la) {
                if ($la->straniPredmet) {
                    $foreignSubjects[] = $la->straniPredmet->naziv;
                    $totalForeignEcts += $la->straniPredmet->ects;
                }
                
                if (!empty($la->ocjena)) {
                    $rawGrade = strtoupper(trim($la->ocjena));
                    // Check map first
                    if (isset($gradeMap[$rawGrade])) {
                        $gradeSum += $gradeMap[$rawGrade];
                        $gradeCount++;
                    } elseif (is_numeric($rawGrade)) {
                        // Fallback if grade is already numeric
                        $gradeSum += (float)$rawGrade;
                        $gradeCount++;
                    }
                }
            }

            if ($gradeCount > 0) {
                $numericGrade = (int) round($gradeSum / $gradeCount);
                $reverseMap = [10 => 'A', 9 => 'B', 8 => 'C', 7 => 'D', 6 => 'E'];
                $grade = $reverseMap[$numericGrade] ?? $numericGrade;
            } else {
                $grade = '';
            }

            $foreignSubjectsString = implode(', ', $foreignSubjects);

            $table->addRow();
            $table->addCell(800)->addText($rowNum++);
            $table->addCell(3000)->addText($fitSubjectName);
            $table->addCell(1000)->addText($semester);
            $table->addCell(1000)->addText($fitEcts);
            $table->addCell(3000)->addText($foreignSubjectsString);
            $table->addCell(1000)->addText($grade);
            $table->addCell(1000)->addText($totalForeignEcts);
        }

        $fileName = 'Mobility_Grades_' . $mobilnost->student->br_indexa . '_' . date('Ymd_His') . '.docx';
        $filePath = storage_path("app/public/$fileName");

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function destroy($id)
    {
        $mobilnost = Mobilnost::findOrFail($id);
        $mobilnost->delete();

        return redirect()->back()->with('success', 'Mobility record deleted successfully.');
    }
}
