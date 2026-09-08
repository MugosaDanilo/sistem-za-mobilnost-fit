<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Validation\Rule;

class FakultetController extends Controller
{
    public function index(Request $request)
    {
        $query = Fakultet::with('univerzitet');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('naziv', 'like', "%{$search}%")
                  ->orWhere('drzava', 'like', "%{$search}%")
                  ->orWhereHas('univerzitet', function($qu) use ($search) {
                      $qu->where('naziv', 'like', "%{$search}%");
                  });
            });
        }

        $fakulteti = $query->paginate(7)->withQueryString();
        $univerziteti = Univerzitet::all(); // For the dropdown in modal

        // Fakulteti sa studentske platforme za povezivanje (dropdown u formi)
        $platformaFakulteti = [];
        if (config('platforma.enabled')) {
            try {
                $platformaFakulteti = app(\App\Services\Platforma\PlatformaClient::class)->sifarnici()['fakulteti'] ?? [];
            } catch (\App\Services\Platforma\PlatformaException $e) {
                session()->now('error', 'Platforma nije dostupna: ' . $e->getMessage());
            }
        }

        return view('fakultet.index', compact('fakulteti', 'univerziteti', 'platformaFakulteti'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'naziv' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:fakulteti,email',
        'drzava' => 'nullable|string|max:255',
        'telefon' => 'required|string|max:255',
        'web' => 'nullable|string|max:255',

        'univerzitet_naziv' => 'nullable|string|max:255', // ime univerziteta iz inputa
        'platforma_fakultet_id' => 'nullable|integer|unique:fakulteti,platforma_fakultet_id',
    ], [
        'email.unique' => 'Fakultet sa ovim emailom već postoji.',
        'platforma_fakultet_id.unique' => 'Taj fakultet sa platforme je već povezan sa drugim fakultetom.',
    ]);

    $fakultet = new Fakultet();
    $fakultet->platforma_fakultet_id = $validated['platforma_fakultet_id'] ?? null;
    $fakultet->naziv = $validated['naziv'];
    $fakultet->drzava = $validated['drzava'] ?? null;
    $fakultet->email = $validated['email'];
    $fakultet->telefon = $validated['telefon'];
    $fakultet->web = $validated['web'] ?? null;


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
        'drzava' => 'nullable|string|max:255',
        'telefon' => 'required|string|max:255',
        'web' => 'nullable|string|max:255',

        'univerzitet_naziv' => 'nullable|string|max:255',
        'file' => 'nullable|file|max:10240', // 10MB max
        'platforma_fakultet_id' => ['nullable', 'integer', Rule::unique('fakulteti')->ignore($fakultet->id)],
    ], [
        'email.unique' => 'Fakultet sa ovim emailom već postoji.',
        'platforma_fakultet_id.unique' => 'Taj fakultet sa platforme je već povezan sa drugim fakultetom.',
    ]);

    $fakultet->platforma_fakultet_id = $validated['platforma_fakultet_id'] ?? null;
    $fakultet->naziv = $validated['naziv'];
    $fakultet->drzava = $validated['drzava'] ?? null;
    $fakultet->email = $validated['email'];
    $fakultet->telefon = $validated['telefon'];
    $fakultet->web = $validated['web'] ?? null;


    if ($request->hasFile('file')) {
        if ($fakultet->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($fakultet->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($fakultet->file_path);
        }
        $path = $request->file('file')->store('fakulteti_files', 'public');
        $fakultet->file_path = $path;
    }

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
        if ($fakultet->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($fakultet->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($fakultet->file_path);
        }
        $fakultet->delete();

        return redirect()->route('fakulteti.index')->with('success', 'Fakultet uspješno obrisan!');
    }

    public function downloadFile($id)
    {
        $fakultet = Fakultet::findOrFail($id);
        if (!$fakultet->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($fakultet->file_path)) {
            return redirect()->back()->with('error', 'Fajl ne postoji.');
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->download($fakultet->file_path);
    }
}
