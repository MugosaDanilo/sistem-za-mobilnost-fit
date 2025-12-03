<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\NivoStudija;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $studenti = Student::with('nivoStudija')->latest()->paginate(15);
        return view('studenti.index', compact('studenti'));
    }

    public function create()
    {
        // Osiguraj da postoje osnovni nivoi studija
        NivoStudija::firstOrCreate(['naziv' => 'Osnovne']);
        NivoStudija::firstOrCreate(['naziv' => 'Master']);
        
        $nivoiStudija = NivoStudija::all();
        return view('studenti.create', compact('nivoiStudija'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'br_indexa' => [
                'required',
                'string',
                'max:255',
                Rule::unique('studenti', 'br_indexa')
            ],
            'datum_rodjenja' => 'required|date',
            'telefon' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('studenti', 'email')
            ],
            'godina_studija' => 'required|integer|min:1|max:8',
            'jmbg' => [
                'required',
                'string',
                'size:13',
                Rule::unique('studenti', 'jmbg')
            ],
            'nivo_studija_id' => 'required|exists:nivo_studija,id',
        ], [
            'br_indexa.unique' => 'Student sa ovim brojem indeksa već postoji.',
            'email.unique' => 'Student sa ovim emailom već postoji.',
            'jmbg.unique' => 'Student sa ovim JMBG-om već postoji.',
            'jmbg.size' => 'JMBG mora imati tačno 13 karaktera.',
        ]);

        Student::create($validated);

        return redirect()->route('studenti.index')->with('success', 'Student uspješno dodat!');
    }

    public function edit($id)
    {
        // Osiguraj da postoje osnovni nivoi studija
        NivoStudija::firstOrCreate(['naziv' => 'Osnovne']);
        NivoStudija::firstOrCreate(['naziv' => 'Master']);
        
        $student = Student::findOrFail($id);
        $nivoiStudija = NivoStudija::all();
        return view('studenti.edit', compact('student', 'nivoiStudija'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'br_indexa' => [
                'required',
                'string',
                'max:255',
                Rule::unique('studenti', 'br_indexa')->ignore($student->id)
            ],
            'datum_rodjenja' => 'required|date',
            'telefon' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('studenti', 'email')->ignore($student->id)
            ],
            'godina_studija' => 'required|integer|min:1|max:8',
            'jmbg' => [
                'required',
                'string',
                'size:13',
                Rule::unique('studenti', 'jmbg')->ignore($student->id)
            ],
            'nivo_studija_id' => 'required|exists:nivo_studija,id',
        ], [
            'br_indexa.unique' => 'Student sa ovim brojem indeksa već postoji.',
            'email.unique' => 'Student sa ovim emailom već postoji.',
            'jmbg.unique' => 'Student sa ovim JMBG-om već postoji.',
            'jmbg.size' => 'JMBG mora imati tačno 13 karaktera.',
        ]);

        $student->update($validated);

        return redirect()->route('studenti.index')->with('success', 'Student uspješno ažuriran!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        try {
            $student->delete();
            return redirect()->route('studenti.index')->with('success', 'Student je uspješno obrisan!');
        } catch (\Illuminate\Database\QueryException $e) {
            if($e->getCode() == '23000') {
                return redirect()->route('studenti.index')->with('error', 'Ne možete obrisati studenta jer postoje povezani zapisi (mobilnosti, prepisi, itd.).');
            }
            throw $e;
        }
    }
}

