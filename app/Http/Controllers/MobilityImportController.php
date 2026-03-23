<?php

namespace App\Http\Controllers;

use App\Models\Fakultet;
use App\Models\Mobilnost;
use App\Models\MobilnostDokument;
use App\Models\MobilityCategory;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use ZipArchive;

class MobilityImportController extends Controller
{
    public function index()
    {
        $fakulteti = Fakultet::orderBy('naziv')->get();
        $documentCategories = MobilityCategory::orderBy('name')->get();

        return view('mobility.import', [
            'fakulteti' => $fakulteti,
            'documentCategories' => $documentCategories,
            'previewData' => null,
            'excelTmpPath' => null,
            'zipTmpPath' => null,
            'selectedFakultetId' => null,
        ]);
    }

    public function preview(Request $request)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('admin.mobility.import');
        }

        $request->validate([
            'fakultet_id' => 'required|exists:fakulteti,id',
            'excel_file' => 'nullable|file|mimes:xlsx,xls',
            'zip_file' => 'nullable|file|mimes:zip',
            'excel_tmp_path' => 'nullable|string',
            'zip_tmp_path' => 'nullable|string',
        ]);

        $fakulteti = Fakultet::orderBy('naziv')->get();
        $documentCategories = MobilityCategory::orderBy('name')->get();
        $selectedFakultet = Fakultet::findOrFail($request->fakultet_id);

        $excelTmpPath = $request->input('excel_tmp_path');
        $zipTmpPath = $request->input('zip_tmp_path');

        if ($request->hasFile('excel_file')) {
            $excelTmpPath = $this->storeTempFile($request->file('excel_file'), 'excel');
        }

        if ($request->hasFile('zip_file')) {
            $zipTmpPath = $this->storeTempFile($request->file('zip_file'), 'zip');
        }

        if (!$excelTmpPath || !Storage::disk('local')->exists($excelTmpPath)) {
            return back()
                ->withErrors(['excel_file' => 'Excel fajl nije dostupan. Uploaduj fajl ponovo.'])
                ->withInput();
        }

        if (!$zipTmpPath || !Storage::disk('local')->exists($zipTmpPath)) {
            return back()
                ->withErrors(['zip_file' => 'ZIP fajl nije dostupan. Uploaduj fajl ponovo.'])
                ->withInput();
        }

        $excelFullPath = Storage::disk('local')->path($excelTmpPath);

        [$header, $dataRows] = $this->readExcel($excelFullPath);
        $normalizedHeader = array_map(fn ($value) => trim((string) $value), $header);

        $requiredColumns = $this->getRequiredColumns();

        $missingColumns = [];
        foreach ($requiredColumns as $requiredColumn) {
            if (!in_array($requiredColumn, $normalizedHeader, true)) {
                $missingColumns[] = $requiredColumn;
            }
        }

        $previewData = [
            'header' => $header,
            'row_count' => count($dataRows),
            'zip_name' => basename($zipTmpPath),
            'missing_columns' => $missingColumns,
            'is_valid_template' => count($missingColumns) === 0,
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'row_errors' => [],
        ];

        $extractPath = null;
        $seenIndexes = [];

        try {
            if (empty($missingColumns)) {
                $headerMap = $this->buildHeaderMap($normalizedHeader);
                $extractPath = $this->extractZipToTemp(Storage::disk('local')->path($zipTmpPath));

                foreach ($dataRows as $rowIndex => $row) {
                    $excelRowNumber = $rowIndex + 2;

                    if ($this->isEmptyRow($row)) {
                        continue;
                    }

                    $brIndeksa = trim((string) $this->getValue($row, $headerMap, 'Br indeksa'));
                    $ime = trim((string) $this->getValue($row, $headerMap, 'Ime'));
                    $prezime = trim((string) $this->getValue($row, $headerMap, 'Prezime'));

                    $duplicateErrors = [];
                    if ($brIndeksa !== '') {
                        if (in_array($brIndeksa, $seenIndexes, true)) {
                            $duplicateErrors[] = 'Broj indeksa se pojavljuje više puta u Excel fajlu.';
                        } else {
                            $seenIndexes[] = $brIndeksa;
                        }
                    }

                    $errors = array_merge(
                        $duplicateErrors,
                        $this->validateImportRow($row, $headerMap, $selectedFakultet, $extractPath)
                    );

                    if (!empty($errors)) {
                        $previewData['invalid_rows']++;
                        $previewData['row_errors'][] = [
                            'row' => $excelRowNumber,
                            'index' => $brIndeksa,
                            'student' => trim($ime . ' ' . $prezime),
                            'errors' => $errors,
                        ];
                    } else {
                        $previewData['valid_rows']++;
                    }
                }
            }
        } finally {
            if ($extractPath) {
                $this->deleteDirectoryIfExists($extractPath);
            }
        }

        return view('mobility.import', [
            'fakulteti' => $fakulteti,
            'documentCategories' => $documentCategories,
            'previewData' => $previewData,
            'excelTmpPath' => $excelTmpPath,
            'zipTmpPath' => $zipTmpPath,
            'selectedFakultetId' => $request->fakultet_id,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'fakultet_id' => 'required|exists:fakulteti,id',
            'excel_tmp_path' => 'required|string',
            'zip_tmp_path' => 'required|string',
        ]);

        $excelTmpPath = $request->input('excel_tmp_path');
        $zipTmpPath = $request->input('zip_tmp_path');

        if (!Storage::disk('local')->exists($excelTmpPath)) {
            return back()->with('error', 'Privremeni Excel fajl ne postoji. Uradi preview ponovo.');
        }

        if (!Storage::disk('local')->exists($zipTmpPath)) {
            return back()->with('error', 'Privremeni ZIP fajl ne postoji. Uradi preview ponovo.');
        }

        $selectedFakultet = Fakultet::findOrFail($request->fakultet_id);
        $excelFullPath = Storage::disk('local')->path($excelTmpPath);
        $zipFullPath = Storage::disk('local')->path($zipTmpPath);

        [$header, $dataRows] = $this->readExcel($excelFullPath);
        $normalizedHeader = array_map(fn ($value) => trim((string) $value), $header);

        foreach ($this->getRequiredColumns() as $requiredColumn) {
            if (!in_array($requiredColumn, $normalizedHeader, true)) {
                return back()->with('error', 'Excel template nije ispravan. Nedostaje kolona: ' . $requiredColumn);
            }
        }

        $headerMap = $this->buildHeaderMap($normalizedHeader);

        $importedCount = 0;
        $skippedCount = 0;
        $errorsByRow = [];
        $extractPath = null;
        $seenIndexes = [];

        try {
            $extractPath = $this->extractZipToTemp($zipFullPath);

            foreach ($dataRows as $rowIndex => $row) {
                $excelRowNumber = $rowIndex + 2;

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $brIndeksa = trim((string) $this->getValue($row, $headerMap, 'Br indeksa'));
                $ime = trim((string) $this->getValue($row, $headerMap, 'Ime'));
                $prezime = trim((string) $this->getValue($row, $headerMap, 'Prezime'));

                $duplicateErrors = [];
                if ($brIndeksa !== '') {
                    if (in_array($brIndeksa, $seenIndexes, true)) {
                        $duplicateErrors[] = 'Broj indeksa se pojavljuje više puta u Excel fajlu.';
                    } else {
                        $seenIndexes[] = $brIndeksa;
                    }
                }

                $errors = array_merge(
                    $duplicateErrors,
                    $this->validateImportRow($row, $headerMap, $selectedFakultet, $extractPath)
                );

                if (!empty($errors)) {
                    $skippedCount++;
                    $errorsByRow[] = "Red {$excelRowNumber} ({$brIndeksa} - {$ime} {$prezime}): " . implode(' ', $errors);
                    continue;
                }

                $student = Student::where('br_indexa', $brIndeksa)->first();

                if (!$student) {
                    $skippedCount++;
                    $errorsByRow[] = "Red {$excelRowNumber} ({$brIndeksa}): student ne postoji u bazi.";
                    continue;
                }

                $datumPocetka = $this->normalizeDateValue($this->getValue($row, $headerMap, 'Datum početka'));
                $datumZavrsetka = $this->normalizeDateValue($this->getValue($row, $headerMap, 'Datum završetka'));
                $tipMobilnosti = trim((string) $this->getValue($row, $headerMap, 'Tip mobilnosti'));
                $studijskaGodina = trim((string) $this->getValue($row, $headerMap, 'Studijska godina'));
                $priznavanje = mb_strtolower(trim((string) $this->getValue($row, $headerMap, 'Priznavanje završeno')));

                $alreadyExists = Mobilnost::where('student_id', $student->id)
                    ->where('fakultet_id', $request->fakultet_id)
                    ->where('datum_pocetka', $datumPocetka)
                    ->where('datum_kraja', $datumZavrsetka)
                    ->exists();

                if ($alreadyExists) {
                    $skippedCount++;
                    $errorsByRow[] = "Red {$excelRowNumber} ({$brIndeksa}): mobilnost već postoji.";
                    continue;
                }

                $documents = $this->resolveStudentDocumentsFromCategories($extractPath, $brIndeksa);

                try {
                    DB::beginTransaction();

                    $mobilnost = Mobilnost::create([
                        'student_id' => $student->id,
                        'fakultet_id' => $request->fakultet_id,
                        'datum_pocetka' => $datumPocetka,
                        'datum_kraja' => $datumZavrsetka,
                        'tip_mobilnosti' => $tipMobilnosti,
                        'studijska_godina' => $studijskaGodina,
                        'is_locked' => false,
                    ]);

                    if ($student->status !== 'mobilnost') {
                        $student->update([
                            'status' => 'mobilnost',
                        ]);
                    }

                    $this->attachDocumentsToMobility($mobilnost, $documents);

                    DB::commit();
                    $importedCount++;
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $skippedCount++;
                    $errorsByRow[] = "Red {$excelRowNumber} ({$brIndeksa}): greška pri importu - " . $e->getMessage();
                }
            }
        } finally {
            if ($extractPath) {
                $this->deleteDirectoryIfExists($extractPath);
            }

            $this->deleteTempFileIfExists($excelTmpPath);
            $this->deleteTempFileIfExists($zipTmpPath);
        }

        if ($importedCount === 0 && !empty($errorsByRow)) {
            return redirect()
                ->route('admin.mobility.import')
                ->with('error', "Nijedna mobilnost nije importovana. Preskočeno: {$skippedCount}.")
                ->with('import_errors', $errorsByRow);
        }

        $message = "Import završen. Uspješno importovano: {$importedCount}. Preskočeno: {$skippedCount}.";

        if (!empty($errorsByRow)) {
            return redirect()
                ->route('admin.mobility.import')
                ->with('success', $message)
                ->with('import_errors', $errorsByRow);
        }

        return redirect()
            ->route('admin.mobility.import')
            ->with('success', $message);
    }

    private function getRequiredColumns(): array
    {
        return [
            'Br indeksa',
            'Ime',
            'Prezime',
            'Host fakultet',
            'Država',
            'Datum početka',
            'Datum završetka',
            'Tip mobilnosti',
            'Studijska godina',
            'Priznavanje završeno',
        ];
    }

    private function validateImportRow(array $row, array $headerMap, Fakultet $selectedFakultet, string $extractPath): array
    {
        $errors = [];

        $brIndeksa = trim((string) $this->getValue($row, $headerMap, 'Br indeksa'));
        $ime = trim((string) $this->getValue($row, $headerMap, 'Ime'));
        $prezime = trim((string) $this->getValue($row, $headerMap, 'Prezime'));
        $hostFakultet = trim((string) $this->getValue($row, $headerMap, 'Host fakultet'));
        $drzava = trim((string) $this->getValue($row, $headerMap, 'Država'));
        $datumPocetka = $this->getValue($row, $headerMap, 'Datum početka');
        $datumZavrsetka = $this->getValue($row, $headerMap, 'Datum završetka');
        $tipMobilnosti = trim((string) $this->getValue($row, $headerMap, 'Tip mobilnosti'));
        $studijskaGodina = trim((string) $this->getValue($row, $headerMap, 'Studijska godina'));
        $priznavanje = mb_strtolower(trim((string) $this->getValue($row, $headerMap, 'Priznavanje završeno')));

        if ($brIndeksa === '') {
            $errors[] = 'Nedostaje "Br indeksa".';
        }

        if ($ime === '') {
            $errors[] = 'Nedostaje "Ime".';
        }

        if ($prezime === '') {
            $errors[] = 'Nedostaje "Prezime".';
        }

        if ($hostFakultet === '') {
            $errors[] = 'Nedostaje "Host fakultet".';
        } elseif (!$this->isMatchingFacultyName($hostFakultet, $selectedFakultet->naziv)) {
            $errors[] = 'Vrijednost "Host fakultet" se ne poklapa sa izabranim fakultetom za import.';
        }

        if ($drzava === '') {
            $errors[] = 'Nedostaje "Država".';
        }

        if ($datumPocetka === null || trim((string) $datumPocetka) === '') {
            $errors[] = 'Nedostaje "Datum početka".';
        } elseif (!$this->isValidDateValue($datumPocetka)) {
            $errors[] = 'Neispravan format za "Datum početka".';
        }

        if ($datumZavrsetka === null || trim((string) $datumZavrsetka) === '') {
            $errors[] = 'Nedostaje "Datum završetka".';
        } elseif (!$this->isValidDateValue($datumZavrsetka)) {
            $errors[] = 'Neispravan format za "Datum završetka".';
        }

        if (
            $this->isValidDateValue($datumPocetka) &&
            $this->isValidDateValue($datumZavrsetka) &&
            strtotime($this->normalizeDateValue($datumPocetka)) > strtotime($this->normalizeDateValue($datumZavrsetka))
        ) {
            $errors[] = '"Datum početka" mora biti prije "Datuma završetka".';
        }

        if ($tipMobilnosti === '') {
            $errors[] = 'Nedostaje "Tip mobilnosti".';
        } elseif (!in_array($tipMobilnosti, ['Semestralna mobilnost', 'Godišnja mobilnost'], true)) {
            $errors[] = 'Neispravna vrijednost za "Tip mobilnosti".';
        }

        if ($studijskaGodina === '') {
            $errors[] = 'Nedostaje "Studijska godina".';
        }

        if ($priznavanje === '') {
            $errors[] = 'Nedostaje "Priznavanje završeno".';
        } elseif (!in_array($priznavanje, ['da', 'ne'], true)) {
            $errors[] = 'Vrijednost za "Priznavanje završeno" mora biti DA ili NE.';
        }

        if ($brIndeksa !== '') {
            $student = Student::where('br_indexa', $brIndeksa)->first();

            if (!$student) {
                $errors[] = 'Student sa brojem indeksa "' . $brIndeksa . '" ne postoji u bazi.';
            } else {
                if (
                    mb_strtolower(trim((string) $student->ime)) !== mb_strtolower($ime) ||
                    mb_strtolower(trim((string) $student->prezime)) !== mb_strtolower($prezime)
                ) {
                    $errors[] = 'Ime i prezime se ne poklapaju sa studentom iz baze za dati broj indeksa.';
                }
            }

            $docCheck = $this->resolveStudentDocumentsFromCategories($extractPath, $brIndeksa);

            foreach ($docCheck['errors'] as $error) {
                $errors[] = $error;
            }

            $normalizedCategoryNames = collect($docCheck['documents'])
                ->pluck('category_name')
                ->map(fn ($name) => $this->normalizeDocumentBaseName($name))
                ->toArray();

            if (!$this->hasRequiredCategory($normalizedCategoryNames, ['learning_agreement', 'la'])) {
                $errors[] = 'Nedostaje dokument kategorije Learning Agreement.';
            }

            if ($priznavanje === 'da') {
                if (!$this->hasRequiredCategory($normalizedCategoryNames, ['tor', 'transcript', 'transcript_of_records'])) {
                    $errors[] = 'Nedostaje dokument kategorije ToR.';
                }

                if (!$this->hasRequiredCategory($normalizedCategoryNames, ['odluka', 'priznavanje', 'odluka_o_priznavanju', 'mobility_decision'])) {
                    $errors[] = 'Nedostaje dokument kategorije Odluka.';
                }
            }
        }

        return array_values(array_unique($errors));
    }

    private function readExcel(string $excelFullPath): array
    {
        $spreadsheet = IOFactory::load($excelFullPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = $rows[0] ?? [];
        $dataRows = array_slice($rows, 1);

        return [$header, $dataRows];
    }

    private function buildHeaderMap(array $normalizedHeader): array
    {
        $headerMap = [];
        foreach ($normalizedHeader as $index => $columnName) {
            $headerMap[$columnName] = $index;
        }

        return $headerMap;
    }

    private function getValue(array $row, array $headerMap, string $columnName): mixed
    {
        $index = $headerMap[$columnName] ?? null;

        return $index !== null ? ($row[$index] ?? null) : null;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function isValidDateValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_numeric($value)) {
            try {
                ExcelDate::excelToDateTimeObject($value);
                return true;
            } catch (\Throwable $e) {
                return false;
            }
        }

        return strtotime($this->normalizeDateValue($value)) !== false;
    }

    private function normalizeDateValue(mixed $value): string
    {
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }

        $value = trim((string) $value);
        $value = str_replace('.', '-', $value);
        $timestamp = strtotime($value);

        return $timestamp ? date('Y-m-d', $timestamp) : $value;
    }

    private function isMatchingFacultyName(string $excelValue, string $selectedFacultyName): bool
    {
        $normalize = fn (string $value) => mb_strtolower(trim(preg_replace('/\s+/', ' ', $value)));

        return $normalize($excelValue) === $normalize($selectedFacultyName);
    }

    private function hasRequiredCategory(array $normalizedCategoryNames, array $acceptedNames): bool
    {
        foreach ($acceptedNames as $acceptedName) {
            if (in_array($this->normalizeDocumentBaseName($acceptedName), $normalizedCategoryNames, true)) {
                return true;
            }
        }

        return false;
    }

    private function getNormalizedCategories(): array
    {
        return MobilityCategory::all()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'normalized_name' => $this->normalizeDocumentBaseName($category->name),
            ];
        })->toArray();
    }

    private function findCategoryForFile(string $filePath, array $categories): ?array
    {
        $baseName = pathinfo($filePath, PATHINFO_FILENAME);
        $normalizedBaseName = $this->normalizeDocumentBaseName($baseName);

        foreach ($categories as $category) {
            if ($normalizedBaseName === $category['normalized_name']) {
                return $category;
            }
        }

        return null;
    }

    private function resolveStudentDocumentsFromCategories(string $extractPath, string $brIndeksa): array
    {
        $errors = [];
        $documents = [];

        $studentFolder = $this->findStudentFolder($extractPath, $brIndeksa);

        if (!$studentFolder) {
            return [
                'errors' => [
                    'Nedostaje folder dokumentacije za studenta. Očekivani naziv foldera je: ' . $this->normalizeIndexForFolder($brIndeksa)
                ],
                'documents' => [],
            ];
        }

        $files = $this->collectFilesFromDirectory($studentFolder);
        $categories = $this->getNormalizedCategories();

        foreach ($files as $filePath) {
            $matchedCategory = $this->findCategoryForFile($filePath, $categories);

            if (!$matchedCategory) {
                $errors[] = 'Fajl "' . basename($filePath) . '" nema odgovarajuću vrstu u bazi.';
                continue;
            }

            $documents[] = [
                'source_path' => $filePath,
                'original_name' => basename($filePath),
                'category_id' => $matchedCategory['id'],
                'category_name' => $matchedCategory['name'],
            ];
        }

        if (empty($documents)) {
            $errors[] = 'Nijedan dokument nije prepoznat na osnovu naziva fajlova i vrsta iz baze.';
        }

        return [
            'errors' => array_values(array_unique($errors)),
            'documents' => $documents,
        ];
    }

    private function findStudentFolder(string $extractPath, string $brIndeksa): ?string
    {
        $normalizedIndex = $this->normalizeIndexForFolder($brIndeksa);
        $directPath = $extractPath . DIRECTORY_SEPARATOR . $normalizedIndex;

        if (is_dir($directPath)) {
            return $directPath;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($extractPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir() && mb_strtolower($item->getFilename()) === mb_strtolower($normalizedIndex)) {
                return $item->getPathname();
            }
        }

        return null;
    }

    private function normalizeIndexForFolder(string $brIndeksa): string
    {
        $normalized = trim($brIndeksa);
        $normalized = str_replace(['\\', '/'], '_', $normalized);
        $normalized = preg_replace('/\s+/', '_', $normalized);

        return $normalized;
    }

    private function collectFilesFromDirectory(string $directory): array
    {
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $extension = mb_strtolower($item->getExtension());
                if (in_array($extension, ['pdf', 'doc', 'docx'], true)) {
                    $files[] = $item->getPathname();
                }
            }
        }

        return $files;
    }

    private function normalizeDocumentBaseName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = str_replace(['č', 'ć', 'š', 'ž', 'đ'], ['c', 'c', 's', 'z', 'dj'], $name);
        $name = preg_replace('/[^a-z0-9]+/u', '_', $name);
        $name = trim($name, '_');

        return $name;
    }

    private function attachDocumentsToMobility(Mobilnost $mobilnost, array $resolvedDocuments): void
    {
        foreach ($resolvedDocuments['documents'] as $document) {
            $destinationFileName = $document['original_name'];
            $relativePath = "mobility_docs/{$mobilnost->id}/{$destinationFileName}";

            Storage::disk('local')->put(
                $relativePath,
                file_get_contents($document['source_path'])
            );

            MobilnostDokument::create([
                'mobilnost_id' => $mobilnost->id,
                'name' => $destinationFileName,
                'path' => $relativePath,
                'type' => $this->normalizeDocumentBaseName(pathinfo($destinationFileName, PATHINFO_FILENAME)),
                'category_id' => $document['category_id'],
            ]);
        }
    }

    private function extractZipToTemp(string $zipFullPath): string
    {
        $extractPath = storage_path('app/tmp/mobility-import-extracted/' . Str::uuid());

        if (!is_dir($extractPath)) {
            mkdir($extractPath, 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipFullPath) !== true) {
            throw new \RuntimeException('Nije moguće otvoriti ZIP fajl.');
        }

        $zip->extractTo($extractPath);
        $zip->close();

        return $extractPath;
    }

    private function storeTempFile($file, string $type): string
    {
        $folder = 'tmp/mobility-import/' . date('Y/m/d');
        $filename = Str::uuid() . '_' . $type . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($folder, $filename, 'local');
    }

    private function deleteTempFileIfExists(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }

    private function deleteDirectoryIfExists(?string $path): void
    {
        if ($path && is_dir($path)) {
            Storage::deleteDirectory(str_replace(storage_path('app') . DIRECTORY_SEPARATOR, '', $path));
        }
    }
}