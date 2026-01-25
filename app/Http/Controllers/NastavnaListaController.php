<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NastavnaLista;

class NastavnaListaController extends Controller
{
    public function show($predmetId)
{
    // Dohvata sve nastavne liste za dati predmet
    $nlList = NastavnaLista::where('predmet_id', $predmetId)->get();

    return view('nastavna_lista.show', compact('nlList'));
}

   
public function store(Request $request)
{
    $request->validate([
        'predmet_id' => 'required|exists:predmeti,id',
        'fakultet_id' => 'required|exists:fakulteti,id',
        'studijska_godina' => 'required|string|max:20',
        'nl_link' => 'nullable|url',
        'nl_file' => 'nullable|file|mimes:pdf,doc,docx',
    ]);

    // Provjerava da li postoji već NL za isti predmet + godinu
    $nastavnaLista = NastavnaLista::updateOrCreate(
        [
            'predmet_id' => $request->predmet_id,
            'fakultet_id' => $request->fakultet_id,
            'studijska_godina' => $request->studijska_godina,
        ],
        [
            'link' => $request->nl_link ?? null,
        ]
    );

    // Ako postoji fajl, dodaj ga i sačuvaj
    if ($request->hasFile('nl_file')) {
        $file = $request->file('nl_file');
        $path = $file->store('nastavne_liste', 'public');

        $nastavnaLista->file_path = $path;
        $nastavnaLista->file_name = $file->getClientOriginalName();
        $nastavnaLista->mime_type = $file->getClientMimeType();
        $nastavnaLista->save();
    }

   return redirect()->route('fakulteti.predmeti.index', $nastavnaLista->fakultet_id)
                 ->with('success', 'Nastavna lista je uspješno sačuvana.');

}



    public function destroy($id)
    {
        $nl = NastavnaLista::findOrFail($id);
        $nl->delete();

        return redirect()->back()->with('success', 'Nastavna lista je obrisana.');
    }

  
    public function edit($id)
    {
        $nl = NastavnaLista::findOrFail($id);
        return response()->json($nl);
    }
}
