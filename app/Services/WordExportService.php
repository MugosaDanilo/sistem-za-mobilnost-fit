<?php

namespace App\Services;

use App\Models\MappingRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use Carbon\Carbon;

class WordExportService
{
    public function generisiPredlog(MappingRequest $request)
    {
        $phpWord = new PhpWord();
        
        // Define styles
        $phpWord->setDefaultFontName('Cambria');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection();

        // 1. Header: Broj & Date
        $headerStyle = ['bold' => false];
        $section->addText("Broj: _________", $headerStyle);
        $section->addText("Podgorica, " . Carbon::now()->format('d.m.Y') . ". godine", $headerStyle);
        $section->addTextBreak(1);

        // 2. Preamble
        $textRun = $section->addTextRun(['alignment' => Jc::BOTH]);
        $textRun->addText("Na osnovu čl. 19. Pravila studiranja na osnovnim studijama Univerziteta „Mediteran“ Podgorica, rješavajući po zahtjevu studenta ");
        
        $studentName = $request->student->ime . " " . $request->student->prezime;
        $textRun->addText($studentName . ", ", ['bold' => true]);
        
        $textRun->addText("dekanici Fakulteta za informacione tehnologije podnosim");
        $section->addTextBreak(1);

        // 3. Title
        $section->addText(
            "PREDLOG RJEŠENJA O PRIZNAVANJU ISPITA SA STRUČNIM MIŠLJENJIMA", 
            ['bold' => true, 'size' => 11], 
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(1);

        // 4. Main Body Intro
        $year = $request->student->godina_studija;
        $yearString = match((int)$year) {
            1 => "prve godine",
            2 => "druge godine",
            3 => "treće godine",
            4 => "četvrte godine",
            default => $year . ". godine"
        };
       
        $foreignFacultyName = $request->fakultet ? $request->fakultet->naziv : "Unknown Faculty";

        $bodyRun = $section->addTextRun(['alignment' => Jc::BOTH]);
        $bodyRun->addText("Studentu ");
        $bodyRun->addText($studentName, ['bold' => true]);
        $bodyRun->addText(", studentu $yearString Fakulteta za informacione tehnologije Univerziteta „Mediteran“ Podgorica, priznaju se položeni ispiti i dobijene ocjene na ");
        $bodyRun->addText($foreignFacultyName, ['bold' => true]);
        $bodyRun->addText(" kao položeni ispiti i dobijene ocjene na Fakultetu za informacione tehnologije Univerziteta „Mediteran“, kako slijedi:");
        $section->addTextBreak(1);

        $matchedSubjects = $request->subjects->filter(function($s) {
            return !is_null($s->fit_predmet_id);
        });

        $grouped = $matchedSubjects->groupBy('professor_id');

        foreach ($grouped as $profId => $subjects) {
            
            $counter = 1;
            foreach ($subjects as $subject) {
                $foreignSubjName = $subject->straniPredmet->naziv;
                
                $studentSubject = $request->student->predmeti->find($subject->strani_predmet_id);
                $grade = $studentSubject ? ($studentSubject->pivot->grade ?? '-') : '-';
                $foreignEcts = $subject->straniPredmet->ects;

                $fitSubjName = $subject->fitPredmet->naziv;
                $fitEcts = $subject->fitPredmet->ects;
                
                $translatedGrade = match((int)$grade) {
                    10 => "„A – odlican",
                    9 => "„B – vrlo dobar“",
                    8 => "„C – dobar“",
                    7 => "„D – zadovoljan“",
                    6 => "„E – dovoljan“",
                    default => "„" . $grade . "“"
                };

                $paragraphStyle = ['tabs' => [new \PhpOffice\PhpWord\Style\Tab('left', 720)]]; // 720 twips = 0.5 inch
                $pRun = $section->addTextRun(['alignment' => Jc::BOTH, 'tabs' => [new \PhpOffice\PhpWord\Style\Tab('left', 720)]]);
                
                // Numbering with Tab
                $pRun->addText($counter . ".\t");
                
                $pRun->addText($foreignSubjName, ['bold' => true]);
                $pRun->addText(", položen na $foreignFacultyName i sa dobijenom ocjenom $translatedGrade, $foreignEcts ECTS, priznaje se kao položen ispit na Fakultetu za informacione tehnologije Univerziteta „Mediteran“ Podgorica pod nazivom ");
                $pRun->addText($fitSubjName, ['bold' => true]);
                $pRun->addText(" sa ocjenom $translatedGrade, $fitEcts ECTS.");
                
                $counter++;
            }
            
            $profName = $subjects->first()->professor->name ?? 'Unknown Professor';
            
            $section->addTextBreak(1);
            $section->addText("______________________________________", ['bold' => true]);
            $section->addText($profName);
            $section->addTextBreak(1);
        }

        // 6. Obrazloženje
        $section->addTextBreak(1);
        $section->addText("Obrazloženje", ['bold' => true, 'underline' => 'single'], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        $obrazlozenjeRun = $section->addTextRun(['alignment' => Jc::BOTH]);
        $obrazlozenjeRun->addText("Uvidom u dostavljenu Molbu studenta ");
 
        $obrazlozenjeRun->addText($studentName . ", ", ['bold' => false]);
        
        $obrazlozenjeRun->addText("Uvjerenje o položenim ispitima i Nastavne planove i programe, a nakon pribavljenih stručnih mišljenja predmetnih nastavnika, koji su sastavni dio ovog akta, predlažem da dekanica Fakulteta za informacione tehnologije Univerziteta „Mediteran“ Podgorica donese ");
        $obrazlozenjeRun->addText("rješenje o priznavanju položenih ispita", ['bold' => true]);
        $obrazlozenjeRun->addText(" navedenih u dispozitivu gore navedenog predloga.");
        
        $section->addTextBreak(2);

        // 7. Final Signature
        $section->addText("Prodekanka za nastavu", [], ['alignment' => Jc::RIGHT]);
        $section->addText("______________________________", [], ['alignment' => Jc::RIGHT]);
        $section->addText("Doc. dr Žana Knežević", ['bold' => true], ['alignment' => Jc::RIGHT]);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'prepis_' . $request->id . '_' . time() . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);
        $objWriter->save($tempPath);

        return $tempPath;
    }

    public function generisiRjesenje(MappingRequest $request)
    {
        $phpWord = new PhpWord();
        
        $phpWord->setDefaultFontName('Cambria');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection();

        $studentName = $request->student->ime . " " . $request->student->prezime;
        $foreignFacultyName = $request->fakultet ? $request->fakultet->naziv : "=";
        $year = $request->student->godina_studija;
        $yearString = match((int)$year) {
            1 => "prve",
            2 => "druge",
            3 => "treće",
            4 => "četvrte",
            default => $year . "."
        };

        $preamble = "Na osnovu člana 84 stav 4 alineja 7 Statuta Univerziteta “Mediteran” Podgorica i člana 19 Pravila studiranja na osnovnim studijama, rješavajući po zahtjevu studenta " . $studentName . ", na predlog prodekanke i nakon pribavljanih stručnih mišljenja predmetnih nastavnika/ca, dekanka Fakulteta donijela je:";
        $section->addText($preamble, [], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        $section->addText("RJEŠENJE", ['bold' => true, 'size' => 12], ['alignment' => Jc::CENTER]);
        $section->addText("o priznavanju položenih ispita", ['bold' => true, 'size' => 11], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        $section->addText("I", ['bold' => true]);
        
        $countryName = $request->fakultet->drzava ?? ($request->fakultet->univerzitet->drzava ?? '');
        $countrySuffix = $countryName ? ", $countryName" : "";

        $introRun = $section->addTextRun(['alignment' => Jc::BOTH]);
        $introRun->addText($studentName, ['bold' => true]);
        $introRun->addText(", studentu $yearString godine Fakulteta za informacione tehnologije Univerziteta „Mediteran” Podgorica, priznaju se položeni ispiti sa fakulteta ");
        $introRun->addText($foreignFacultyName . $countrySuffix, ['bold' => true]);
        $introRun->addText(", kao položeni ispiti na fakultetu Fakulteta za informacione tehnologije Univerziteta „Mediteran” Podgorica, na sljedeći način:");
        $section->addTextBreak(1);

        $matchedSubjects = $request->subjects->filter(fn($s) => !is_null($s->fit_predmet_id));
        $counter = 1;

        foreach ($matchedSubjects as $subject) {
            $foreignSubjName = $subject->straniPredmet->naziv;
            $studentSubject = $request->student->predmeti->find($subject->strani_predmet_id);
            $grade = $studentSubject ? ($studentSubject->pivot->grade ?? '-') : '-';
            $foreignEcts = $subject->straniPredmet->ects;

            $fitSubjName = $subject->fitPredmet->naziv;
            $fitEcts = $subject->fitPredmet->ects;

            $translatedGrade = match((int)$grade) {
                10 => "10 (A – odličan)",
                9 => "9 (B – vrlo dobar)",
                8 => "8 (C – dobar)",
                7 => "7 (D – zadovoljan)",
                6 => "6 (E – dovoljan)",
                default => $grade
            };

            $textRun = $section->addTextRun(['alignment' => Jc::BOTH]);
            $textRun->addText($counter . ". Ispit ");
            $textRun->addText($foreignSubjName, ['italic' => true]);
            $textRun->addText(", položen na fakultetu $foreignFacultyName, vrednovan sa $foreignEcts ECTS kredita i ostvarenom ocjenom $translatedGrade, priznaje se kao položeni ispit na fakultetu Univerziteta “Mediteran” Podgorica pod nazivom ");
            $textRun->addText($fitSubjName, ['italic' => true, 'bold' => true]);
            $textRun->addText(", vrednovan sa $fitEcts ECTS i ocjenom $translatedGrade.", ['bold' => true]);
            
            $counter++;
        }
        $section->addTextBreak(1);

        $section->addText("II     Rješenje stupa na snagu danom donošenja.");
        $section->addText("III   Odluka je konačna na Univerzitetu.");
        $section->addTextBreak(1);

        $section->addText("Obrazloženje", ['bold' => true], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        $obrazlozenjeRun = $section->addTextRun(['alignment' => Jc::BOTH]);
        $obrazlozenjeRun->addText("Student $yearString godine osnovnih akademskih studija $foreignFacultyName, ");
        $obrazlozenjeRun->addText($studentName, ['bold' => true]);
        $obrazlozenjeRun->addText(", obratio se dekanki Fakulteta za informacione tehnologije, u cilju priznavanja navedenih ispita položenih na $foreignFacultyName. Imenovani student je, u prilog tome, dostavio i Uvjerenje o položenim ispitima na od $foreignFacultyName.");
        $section->addTextBreak(1);

        $obrazlozenjeRun2 = $section->addTextRun(['alignment' => Jc::BOTH]);
        $obrazlozenjeRun2->addText("Uvidom u službenu dokumentaciju imenovanog studenta, kao i uvidom u Nastavni plan i program Fakulteta, prodekanka Fakulteta za informacione tehnologije, donijela je predlog za priznavanje traženih ispita, uz prethodno pribavljanje stručnog mišljenja predmetnih nastavnika o priznavanju navedenih predmeta. Dekanka Fakulteta za informacione tehnologije Univerziteta „Mediteran“ Podgorica je, u smislu člana 19 Pravila studiranja na osnovnim studijama, i priavljenih stručnih mišljenja predmetnih nastavnika/ca, a na osnovu predloga prodekanke, odlučila kao u dispozitivu Rješenja.");
        $section->addTextBreak(1);

        $section->addText("Pravna pouka: Protiv ovog rješenja može se izjaviti žalba Vijeću Fakulteta u roku od 15 dana od prijema Rješenja.");
        $section->addTextBreak(2);

        $footerTable = $section->addTable(['width' => 100 * 50, 'unit' => 'pct']);
        $footerTable->addRow();
        
        $leftCell = $footerTable->addCell(5000);
        $leftCell->addText("Dostavljeno :");
        $leftCell->addText("-studentu " . $studentName);
        $leftCell->addText("-u evidencioni karton studenta");
        $leftCell->addText("-arhivi Fakulteta");

        $rightCell = $footerTable->addCell(5000);
        $rightCell->addText("DEKANKA", ['bold' => true], ['alignment' => Jc::RIGHT]);
        $rightCell->addText("prof. dr Maja Delibašić", ['bold' => true], ['alignment' => Jc::RIGHT]);

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $fileName = 'rjesenje_' . $request->id . '_' . time() . '.docx';
        $tempPath = storage_path('app/public/' . $fileName);
        $objWriter->save($tempPath);

        return $tempPath;
    }
}
