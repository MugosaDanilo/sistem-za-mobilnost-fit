<?php

namespace App\Http\Controllers;

use App\Models\Fakultet;
use App\Models\Predmet;
use App\Models\Prepis;
use App\Models\PrepisAgreement;
use App\Models\Student;
use Illuminate\Http\Request;

class PrepisController extends Controller
{
    public function index()
    {
        $prepisi = Prepis::with(['student', 'fakultet'])->latest()->get();
        return view('prepis.index', compact('prepisi'));
    }

    public function create()
    {
        $studenti = Student::all();
        $fakulteti = Fakultet::all();
        $predmeti = Predmet::select('id', 'naziv', 'ects', 'fakultet_id')->get();

        return view('prepis.create', compact('studenti', 'fakulteti', 'predmeti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:studenti,id',
            'fakultet_id' => 'required|exists:fakulteti,id',
            'datum' => 'required|date',
            'agreements' => 'required|array',
            'agreements.*.fit_predmet_id' => 'required|exists:predmeti,id',
            'agreements.*.strani_predmet_id' => 'required|exists:predmeti,id',
        ]);

        $prepis = Prepis::create([
            'student_id' => $request->student_id,
            'fakultet_id' => $request->fakultet_id,
            'datum' => $request->datum,
            'status' => 'u procesu',
        ]);

        foreach ($request->agreements as $agreementData) {
            PrepisAgreement::create([
                'prepis_id' => $prepis->id,
                'fit_predmet_id' => $agreementData['fit_predmet_id'],
                'strani_predmet_id' => $agreementData['strani_predmet_id'],
                'status' => 'u procesu',
            ]);
        }

        return redirect()->route('prepis.index')->with('success', 'Prepis created successfully.');
    }

    public function edit($id)
    {
        $prepis = Prepis::with(['student', 'fakultet', 'agreements.fitPredmet', 'agreements.straniPredmet'])->findOrFail($id);
        $studenti = Student::all();
        $fakulteti = Fakultet::all();
        $predmeti = Predmet::select('id', 'naziv', 'ects', 'fakultet_id')->get();

        return view('prepis.edit', compact('prepis', 'studenti', 'fakulteti', 'predmeti'));
    }

    public function update(Request $request, $id)
    {
        $prepis = Prepis::findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:studenti,id',
            'fakultet_id' => 'required|exists:fakulteti,id',
            'datum' => 'required|date',
            'agreements' => 'nullable|array',
            'agreements.*.fit_predmet_id' => 'required|exists:predmeti,id',
            'agreements.*.strani_predmet_id' => 'required|exists:predmeti,id',
        ]);

        $prepis->update([
            'student_id' => $request->student_id,
            'fakultet_id' => $request->fakultet_id,
            'datum' => $request->datum,
        ]);

        $prepis->agreements()->delete();

        if ($request->has('agreements')) {
            foreach ($request->agreements as $agreementData) {
                PrepisAgreement::create([
                    'prepis_id' => $prepis->id,
                    'fit_predmet_id' => $agreementData['fit_predmet_id'],
                    'strani_predmet_id' => $agreementData['strani_predmet_id'],
                    'status' => 'u procesu',
                ]);
            }
        }

        return redirect()->route('prepis.index')->with('success', 'Prepis updated successfully.');
    }

    public function destroy($id)
    {
        $prepis = Prepis::findOrFail($id);
        $prepis->agreements()->delete();
        $prepis->delete();
        return redirect()->route('prepis.index')->with('success', 'Prepis deleted successfully.');
    }

    public function show($id)
    {
        $prepis = Prepis::with(['student', 'fakultet', 'agreements.fitPredmet', 'agreements.straniPredmet'])->findOrFail($id);
        return view('prepis.show', compact('prepis'));
    }

    public function getAutomecSuggestions(Request $request)
    {
        $request->validate([
            'strani_predmet_ids' => 'required|array',
            'strani_predmet_ids.*' => 'exists:predmeti,id',
            'fakultet_id' => 'nullable|exists:fakulteti,id',
        ]);

        $straniPredmetIds = $request->strani_predmet_ids;
        $fakultetId = $request->fakultet_id;
        $suggestions = [];

        foreach ($straniPredmetIds as $straniPredmetId) {
            // Nadji automec
            $query = PrepisAgreement::where('strani_predmet_id', $straniPredmetId)
                ->whereNotNull('fit_predmet_id');

            // Ako ima taj fakultet_id, filtriraj prepise sa tim fakultetom
            if ($fakultetId) {
                $query->whereHas('prepis', function ($q) use ($fakultetId) {
                    $q->where('fakultet_id', $fakultetId);
                });
            }

            // Grupiši

            $pairings = $query->selectRaw('fit_predmet_id, COUNT(*) as count')
                ->groupBy('fit_predmet_id')
                ->orderByDesc('count')
                ->first();

            if ($pairings && $pairings->fit_predmet_id) {
                $fitPredmet = \App\Models\Predmet::find($pairings->fit_predmet_id);
                if ($fitPredmet) {
                    $suggestions[$straniPredmetId] = [
                        'fit_predmet_id' => $fitPredmet->id,
                        'count' => $pairings->count,
                    ];
                }
            }
        }

        return response()->json($suggestions);
    }
}
