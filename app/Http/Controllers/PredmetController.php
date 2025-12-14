<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Predmet;
use App\Models\Fakultet;
use Illuminate\Support\Facades\DB;

class PredmetController extends Controller
{
    public function index(Fakultet $fakultet)
    {
        $predmeti = $fakultet->predmeti;
        return view('predmet.index', compact('predmeti', 'fakultet'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'ects' => 'required|integer|min:1',
            'semestar' => 'required|integer|min:1',
            'fakultet_id' => 'required|exists:fakulteti,id',
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
        ]);

        $predmet->update($validated);

        return redirect()->route('fakulteti.predmeti.index', $predmet->fakultet_id)
            ->with('success', 'Predmet uspješno ažuriran!');
    }

    public function destroy($id)
    {
        $predmet = Predmet::findOrFail($id);
        $fakultetId = $predmet->fakultet_id;
        $predmet->delete();

        return redirect()->route('fakulteti.predmeti.index', $fakultetId)
            ->with('success', 'Predmet uspješno obrisan!');
    }

    public function importCsv(Request $request, Fakultet $fakultet)
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $path = $request->file('csv')->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return back()->withErrors(['csv' => 'Unable to open uploaded CSV file.']);
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return back()->withErrors(['csv' => 'CSV file is empty.']);
        }

        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            return back()->withErrors(['csv' => 'Invalid CSV header.']);
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $idxNaziv = array_search('naziv', $header, true);
        $idxEcts = array_search('ects', $header, true);
        $idxSemestar = array_search('semestar', $header, true); 

        if ($idxNaziv === false || $idxEcts === false) {
            fclose($handle);
            return back()->withErrors(['csv' => 'CSV must contain header columns: naziv, ects (optional: semestar).']);
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $naziv = isset($row[$idxNaziv]) ? trim($row[$idxNaziv]) : '';
                $ectsRaw = isset($row[$idxEcts]) ? trim($row[$idxEcts]) : '';
                $semestarRaw = ($idxSemestar !== false && isset($row[$idxSemestar])) ? trim($row[$idxSemestar]) : '';

                if ($naziv === '' || $ectsRaw === '') {
                    $skipped++;
                    continue;
                }

                $ects = (int) $ectsRaw;
                if ($ects < 1) {
                    $skipped++;
                    continue;
                }

                $semestar = $semestarRaw !== '' ? (int) $semestarRaw : 1;
                if ($semestar < 1) {
                    $semestar = 1;
                }

                $existing = Predmet::where('fakultet_id', $fakultet->id)
                    ->where('naziv', $naziv)
                    ->first();

                if (!$existing) {
                    Predmet::create([
                        'naziv' => $naziv,
                        'ects' => $ects,
                        'semestar' => $semestar,
                        'fakultet_id' => $fakultet->id,
                    ]);
                    $created++;
                } else {
                    $existing->update([
                        'ects' => $ects,
                        'semestar' => $semestar,
                    ]);
                    $updated++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            throw $e;
        }

        fclose($handle);

        return back()->with('success', "CSV import completed. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }
}
