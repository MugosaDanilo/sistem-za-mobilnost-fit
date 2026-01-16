<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\NivoStudija;
use Illuminate\Http\Request;

class StudentController extends Controller
{
  public function index()
  {
    $students = Student::with(['nivoStudija', 'fakultet'])->orderBy('created_at', 'desc')->get();
    $nivoStudija = NivoStudija::all();

    return view('students.index', compact('students', 'nivoStudija'));
  }

  public function create()
  {
    $nivoStudija = NivoStudija::all();
    $fakulteti = \App\Models\Fakultet::orderBy('naziv')->get();
    // Default to FIT subjects initially or empty, let frontend handle it via JS
    $predmeti = \App\Models\Predmet::whereHas('fakultet', function ($query) {
      $query->where('naziv', 'FIT');
    })->get();
    return view('students.create', compact('nivoStudija', 'predmeti', 'fakulteti'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'ime' => 'required|string|max:255',
      'prezime' => 'required|string|max:255',
      'br_indexa' => 'required|string|max:20|unique:studenti',
      'datum_rodjenja' => 'required|date',
      'telefon' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:studenti,email',
      'godina_studija' => 'required|integer',
      'jmbg' => 'required|string|size:13|unique:studenti,jmbg',
      'nivo_studija_id' => 'required|exists:nivo_studija,id',
      'fakultet_id' => 'required|exists:fakulteti,id',
      'pol' => 'required|string|in:musko,zensko',
      'predmeti' => 'array',
      'predmeti.*' => 'array', // Each item in predmeti should be an array (e.g., ['grade' => 7])
      'predmeti.*.grade' => 'nullable|integer|min:6|max:10', // Validate grades if present
    ]);

    $student = Student::create($validated);

    if ($request->has('predmeti')) {
      $syncData = [];
      foreach ($request->predmeti as $id => $data) {
        // Ensure the ID itself is a valid subject ID before syncing
        if (\App\Models\Predmet::where('id', $id)->exists()) {
          $syncData[$id] = ['grade' => $data['grade'] ?? null];
        }
      }
      $student->predmeti()->sync($syncData);
    }

    return redirect()->route('students.index')
      ->with('success', 'Student created successfully!');
  }

  public function edit($id)
  {
    $student = Student::with('predmeti')->findOrFail($id);
    $nivoStudija = NivoStudija::all();
    $fakulteti = \App\Models\Fakultet::orderBy('naziv')->get();
    // Fetch subjects for the student's current faculty or all? 
    // The view will likely need to fetch subjects based on selection.
    // For now, let's pass FIT subjects as default pool if we want, 
    // OR we should pass ALL subjects that the student HAS, plus the pool from selected faculty.
    // The <x-subject-selector> might need all available subjects to show them checked.
    // BUT if we change faculty, the list changes.
    // Let's pass FIT subjects as default pool for now.
    $predmeti = \App\Models\Predmet::whereHas('fakultet', function ($query) {
      $query->where('naziv', 'FIT');
    })->get();
    
    return view('students.edit', compact('student', 'nivoStudija', 'predmeti', 'fakulteti'));
  }

  public function update(Request $request, $id)
  {
    $student = Student::findOrFail($id);

    $validated = $request->validate([
      'ime' => 'required|string|max:255',
      'prezime' => 'required|string|max:255',
      'br_indexa' => 'required|string|max:20|unique:studenti,br_indexa,' . $id,
      'datum_rodjenja' => 'required|date',
      'telefon' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:studenti,email,' . $id,
      'godina_studija' => 'required|integer',
      'jmbg' => 'required|string|size:13|unique:studenti,jmbg,' . $id,
      'nivo_studija_id' => 'required|exists:nivo_studija,id',
      'fakultet_id' => 'required|exists:fakulteti,id',
      'pol' => 'required|string|in:musko,zensko',
      'predmeti' => 'array',
      'predmeti.*' => 'array', // Each item in predmeti should be an array (e.g., ['grade' => 7])
      'predmeti.*.grade' => 'nullable|integer|min:6|max:10', // Validate grades if present
    ]);

    $student->update($validated);

    if ($request->has('predmeti')) {
      $syncData = [];
      foreach ($request->predmeti as $id => $data) {
        if (!is_array($data)) {
          // If $data is not an array, assume it's just the subject ID
          // and set grade to null. This handles cases where only subject IDs are sent.
          $syncData[$data] = ['grade' => null];
        } else {
          // If $data is an array, assume it contains 'grade'
          $syncData[$id] = ['grade' => $data['grade'] ?? null];
        }
      }
      $student->predmeti()->sync($syncData);
    } else {
      $student->predmeti()->detach();
    }

    return redirect()->route('students.index')
      ->with('success', 'Student updated successfully!');
  }

  public function destroy($id)
  {
    $student = Student::findOrFail($id);
    $student->delete();

    return redirect()->route('students.index')
      ->with('success', 'Student deleted successfully!');
  }
}
