<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Univerzitet;
use Illuminate\Validation\Rule;



class UniverzitetController extends Controller
{
  public function create()
    {
        return view('univerzitet.create');
    }

    public function index()
    {
        $univerziteti = Univerzitet::all();
    return view('univerzitet.index', compact('univerziteti'));
    }

    public function store(Request $request)
    {
          $validated = $request->validate([
        'naziv' => 'required|string|max:255',
        'drzava' => 'required|string|max:255',
        'grad' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('univerziteti', 'email')
        ],
    ], [
        'email.unique' => 'Univerzitet sa ovim emailom već postoji u bazi.',
    ]);

        $univerzitet = Univerzitet::create($validated);


            return redirect()->back()->with('success', 'Univerzitet uspješno dodat!');

    }

  

    public function show($id)
    {
        return Univerzitet::with('fakulteti')->findOrFail($id);
    }

public function edit($id)
{
    $univerzitet = Univerzitet::findOrFail($id);
    return view('univerzitet.edit', compact('univerzitet'));
}

public function update(Request $request, $id)
{
    $univerzitet = Univerzitet::findOrFail($id);

    $validated = $request->validate([
        'naziv' => 'required|string|max:255',
        'drzava' => 'required|string|max:255',
        'grad' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('univerziteti')->ignore($univerzitet->id),
        ],
    ], [
        'email.unique' => 'Univerzitet sa ovim emailom već postoji u bazi.',
    ]);

    $univerzitet->update($validated);

    return redirect()->route('univerzitet.index')->with('success', 'Univerzitet uspješno ažuriran!');
}


public function destroy($id)
{
    $univerzitet = Univerzitet::findOrFail($id);

    try {
        $univerzitet->delete();
        return redirect()->route('univerzitet.index')->with('success', 'Univerzitet je uspješno obrisan!');
    } catch (\Illuminate\Database\QueryException $e) {
        // Provjera da li je zbog foreign key constraint-a
        if($e->getCode() == '23000') {
            return redirect()->route('univerzitet.index')->with('error', 'Ne možete obrisati univerzitet jer postoje fakulteti koji pripadaju njemu.');
        }
        throw $e; // Ostale greške ponovo baci
    }
}

public function bulkDelete(Request $request)
{
    $ids = explode(',', $request->ids); // pretvori string u niz ID-jeva
    if(!empty($ids)) {
        Univerzitet::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', 'Selected universities deleted successfully.');
    }
    return redirect()->back()->with('error', 'No universities selected.');
}

}
