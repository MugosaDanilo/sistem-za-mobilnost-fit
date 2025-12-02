<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $studenti = Student::orderByDesc('id')->paginate(10);
        return view('studenti.index', compact('studenti'));
    }

    public function create(): View
    {
        return view('studenti.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validacija($request);

        Student::create($data);

        return redirect()
            ->route('studenti.index')
            ->with('success', 'Student je sačuvan.');
    }

    public function edit(Student $studenti): View
    {
        return view('studenti.edit', ['student' => $studenti]);
    }

    public function update(Request $request, Student $studenti): RedirectResponse
    {
        $data = $this->validacija($request, $studenti->id);

        $studenti->update($data);

        return redirect()
            ->route('studenti.index')
            ->with('success', 'Podaci su ažurirani.');
    }

    public function destroy(Student $studenti): RedirectResponse
    {
        $studenti->delete();

        return redirect()
            ->route('studenti.index')
            ->with('success', 'Student je obrisan.');
    }

    private function validacija(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'ime'            => ['required','string','max:100'],
            'prezime'        => ['required','string','max:100'],
            'broj_indeksa'   => ['required','string','max:50','unique:students,broj_indeksa,'.($id ?? 'NULL').',id'],
            'email'          => ['required','email','max:255','unique:students,email,'.($id ?? 'NULL').',id'],
            'telefon'        => ['nullable','string','max:50'],
            'datum_rodjenja' => ['nullable','date','before:today'],
            'godina_studija' => ['nullable','integer','min:1','max:8'],
            'napomena'       => ['nullable','string'],
        ]);
    }
}
