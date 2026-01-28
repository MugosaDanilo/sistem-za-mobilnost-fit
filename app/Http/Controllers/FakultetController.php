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
        'univerzitet_naziv' => 'nullable|string|max:255', // ime univerziteta iz inputa
    ], [
        'email.unique' => 'Fakultet sa ovim emailom već postoji.',
    ]);

    $fakultet = new Fakultet();
    $fakultet->naziv = $validated['naziv'];
    $fakultet->email = $validated['email'];
    $fakultet->telefon = $validated['telefon'];
    $fakultet->web = $validated['web'] ?? null;
    $fakultet->uputstvo_za_ocjene = $validated['uputstvo_za_ocjene'] ?? null;

   // Ako korisnik unese novi univerzitet
if ($request->filled('new_univerzitet')) {
    $univerzitet = Univerzitet::firstOrCreate([
        'naziv' => $request->new_univerzitet
    ]);
    $fakultet->univerzitet_id = $univerzitet->id;
} else {
    // Ako je select popunjen, uzmi ID iz selecta
    $fakultet->univerzitet_id = $request->univerzitet_id ?: null;
}

    $fakultet->save();

    return redirect()->back()->with('success', 'Fakultet uspješno dodat!');
}

public function update(Request $request, $id)
{
    $fakultet = Fakultet::findOrFail($id);

    $validated = $request->validate([
        'naziv' => 'required|string|max:255',
        'email' => ['required','email','max:255', Rule::unique('fakulteti')->ignore($fakultet->id)],
        'telefon' => 'required|string|max:255',
        'web' => 'nullable|string|max:255',
        'uputstvo_za_ocjene' => 'nullable|string',
        'univerzitet_naziv' => 'nullable|string|max:255',
    ], [
        'email.unique' => 'Fakultet sa ovim emailom već postoji.',
    ]);

    $fakultet->naziv = $validated['naziv'];
    $fakultet->email = $validated['email'];
    $fakultet->telefon = $validated['telefon'];
    $fakultet->web = $validated['web'] ?? null;
    $fakultet->uputstvo_za_ocjene = $validated['uputstvo_za_ocjene'] ?? null;

    // Ako korisnik unese novi univerzitet
if ($request->filled('new_univerzitet')) {
    $univerzitet = Univerzitet::firstOrCreate([
        'naziv' => $request->new_univerzitet
    ]);
    $fakultet->univerzitet_id = $univerzitet->id;
} else {
    // Ako je select popunjen, uzmi ID iz selecta
    $fakultet->univerzitet_id = $request->univerzitet_id ?: null;
}

    $fakultet->save();

    return redirect()->route('fakulteti.index')->with('success', 'Fakultet uspješno ažuriran!');
}


    public function destroy($id)
    {
        $fakultet = Fakultet::findOrFail($id);
        $fakultet->delete();

        return redirect()->route('fakulteti.index')->with('success', 'Fakultet uspješno obrisan!');
    }
}
