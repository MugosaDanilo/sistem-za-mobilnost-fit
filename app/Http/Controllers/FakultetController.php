<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Validation\Rule;

class FakultetController extends Controller
{
    public function index()
    {
        $fakulteti = Fakultet::with('univerzitet')->get();
        $univerziteti = Univerzitet::all(); // For the dropdown in modal
        return view('fakultet.index', compact('fakulteti', 'univerziteti'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:fakulteti,email',
            'telefon' => 'required|string|max:255',
            'web' => 'nullable|string|max:255',
            'uputstvo_za_ocjene' => 'nullable|string',
            'univerzitet_id' => 'required|exists:univerziteti,id',
        ], [
            'email.unique' => 'Fakultet sa ovim emailom već postoji.',
            'univerzitet_id.exists' => 'Izabrani univerzitet ne postoji.',
        ]);

        Fakultet::create($validated);

        return redirect()->back()->with('success', 'Fakultet uspješno dodat!');
    }

    public function update(Request $request, $id)
    {
        $fakultet = Fakultet::findOrFail($id);

        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('fakulteti')->ignore($fakultet->id),
            ],
            'telefon' => 'required|string|max:255',
            'web' => 'nullable|string|max:255',
            'uputstvo_za_ocjene' => 'nullable|string',
            'univerzitet_id' => 'required|exists:univerziteti,id',
        ], [
            'email.unique' => 'Fakultet sa ovim emailom već postoji.',
        ]);

        $fakultet->update($validated);

        return redirect()->route('fakulteti.index')->with('success', 'Fakultet uspješno ažuriran!');
    }

    public function destroy($id)
    {
        $fakultet = Fakultet::findOrFail($id);
        $fakultet->delete();

        return redirect()->route('fakulteti.index')->with('success', 'Fakultet uspješno obrisan!');
    }

  

public function bulkDelete(Request $request)
{
     $ids = $request->ids;

    if (!$ids || !is_array($ids)) {
        return redirect()->back()->with('error', 'Nije odabran nijedan fakultet za brisanje.');
    }

    try {
        Fakultet::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Odabrani fakulteti su uspješno obrisani.');
    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() == 23000) { // foreign key constraint
            return redirect()->back()->with('error', 'Neki fakulteti ne mogu biti obrisani jer imaju povezane predmete ili mobilnosti.');
        }
        return redirect()->back()->with('error', 'Došlo je do greške prilikom brisanja fakulteta.');
    }
}



}
