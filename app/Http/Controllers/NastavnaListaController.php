<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NastavnaLista;

class NastavnaListaController extends Controller
{
   
    public function store(Request $request)
{
    $request->validate([
        'predmet_id' => 'required|exists:predmeti,id',
        'fakultet_id' => 'required|exists:fakulteti,id',
        'nl_link' => 'required|url',
    ]);

    NastavnaLista::updateOrCreate(
        [
            'predmet_id' => $request->predmet_id,
            'fakultet_id' => $request->fakultet_id,
        ],
        [
            'link' => $request->nl_link
        ]
    );

    return redirect()->back()->with('success', 'Nastavna lista je sačuvana.');
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
