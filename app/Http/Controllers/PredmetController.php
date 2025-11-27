<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Predmet;
use App\Models\Fakultet;

class PredmetController extends Controller
{
    public function index(Fakultet $fakultet)
    {
        $predmeti = $fakultet->predmeti; // Get subjects for this faculty
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

        return redirect()->route('fakulteti.predmeti.index', $predmet->fakultet_id)->with('success', 'Predmet uspješno ažuriran!');
    }

    public function destroy($id)
    {
        $predmet = Predmet::findOrFail($id);
        $fakultetId = $predmet->fakultet_id; // Save ID before deleting
        $predmet->delete();

        return redirect()->route('fakulteti.predmeti.index', $fakultetId)->with('success', 'Predmet uspješno obrisan!');
    }
}
