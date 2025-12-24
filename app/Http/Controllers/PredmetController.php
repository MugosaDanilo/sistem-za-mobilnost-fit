<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Predmet;
use App\Models\Fakultet;

class PredmetController extends Controller
{
    public function index(Fakultet $fakultet)
    {
        $predmeti = $fakultet->predmeti()->with('nivoStudija')->get();
        $nivoStudija = \App\Models\NivoStudija::all();
        return view('predmet.index', compact('predmeti', 'fakultet', 'nivoStudija'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'ects' => 'required|integer|min:1',
            'semestar' => 'required|integer|min:1',
            'fakultet_id' => 'required|exists:fakulteti,id',
            'nivo_studija_id' => 'required|exists:nivo_studija,id',
        ]);

        Predmet::create($validated);

        return redirect()->back()->with('success', 'Predmet uspješno dodat!');
    }

    public function update(Request $request, $id)
    {
        $predmet = Predmet::findOrFail($id);

        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'ects' => 'required|integer|min:1',
            'semestar' => 'required|integer|min:1',
            'fakultet_id' => 'required|exists:fakulteti,id',
            'nivo_studija_id' => 'required|exists:nivo_studija,id',
        ]);

        $predmet->update($validated);

        return redirect()->route('fakulteti.predmeti.index', $predmet->fakultet_id)->with('success', 'Predmet uspješno ažuriran!');
    }

    public function destroy($id)
    {
        $predmet = Predmet::findOrFail($id);
        $fakultetId = $predmet->fakultet_id; // Save ID before deleting
        $predmet->delete();

        return redirect()->route('fakulteti.predmeti.index', $fakultetId)->with('success', 'Predmet uspješno obrisan!');
    }

    public function import(Request $request, $fakultetId)
    {
        $request->validate([
            'file' => 'required|mimes:docx|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $filePath = $file->getPathname();

        $importer = new \App\Services\SubjectImportService();
        try {
            $courses = $importer->loadCoursesFromFit($filePath);
        } catch (\Exception $e) {
             return redirect()->back()->withErrors(['file' => 'Error parsing file: ' . $e->getMessage()]);
        }

        $fakultet = Fakultet::findOrFail($fakultetId);
        $osnovne = \App\Models\NivoStudija::where('naziv', 'Osnovne')->first(); // Assuming Import is for Osnovne logic as per seeder
        
        $count = 0;
        // Group courses by fingerprint to handle identical duplicates
        $courseGroups = [];
        foreach ($courses as $c) {
            $key = ($c['Naziv predmeta'] ?? 'Unknown') . '|' . ($importer->romanToInt($c['Semestar'] ?? '')) . '|' . ((int) ($c['ECTS'] ?? 0));
            if (!isset($courseGroups[$key])) {
                $courseGroups[$key] = [];
            }
            $courseGroups[$key][] = $c;
        }

        $count = 0;
        foreach ($courseGroups as $key => $groupCourses) {
            // Find existing subjects matches for this key
            // We assume name, semester, ects match.
            // Safe bet: We want to match existing DB records to File records 1-to-1.
            $first = $groupCourses[0];
            $name = $first['Naziv predmeta'] ?? 'Unknown';
            $sem = $importer->romanToInt($first['Semestar'] ?? '');
            $ects = (int) ($first['ECTS'] ?? 0);
            
            // Get all existing subjects with these Exact attributes
            $existing = Predmet::where('fakultet_id', $fakultet->id)
                                ->where('naziv', $name)
                                ->where('semestar', $sem)
                                ->where('ects', $ects)
                                ->get();
            
            // Sync logic:
            // If File has 3, DB has 1 -> Create 2.
            // If File has 3, DB has 3 -> Create 0.
            // If File has 3, DB has 0 -> Create 3.
            
            $needed = count($groupCourses) - $existing->count();
            
            // Helper to get English name (assuming all duplicates have same English name)
            $engName = $first['Naziv predmeta(Eng)'] ?? null;
            
            // Update EXISTING records with new data (e.g. English name)
            foreach($existing as $exSubject) {
                $exSubject->update([
                    'naziv_engleski' => $engName,
                    'nivo_studija_id' => $osnovne->id ?? null,
                ]);
            }

            if ($needed > 0) {
                for ($i = 0; $i < $needed; $i++) {
                     Predmet::create([
                        'naziv' => $name,
                        'fakultet_id' => $fakultet->id,
                        'semestar' => $sem,
                        'ects' => $ects,
                        'naziv_engleski' => $engName,
                        'nivo_studija_id' => $osnovne->id ?? null,
                     ]);
                     $count++;
                }
            }
        }

        return redirect()->back()->with('success', "Successfully imported $count new subjects!");
    }
}
