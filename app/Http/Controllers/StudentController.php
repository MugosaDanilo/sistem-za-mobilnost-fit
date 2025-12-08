<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\NivoStudija;
use Illuminate\Http\Request;

class StudentController extends Controller
{
  public function index()
  {
    $students = Student::with('nivoStudija')->orderBy('created_at', 'desc')->get();
    $nivoStudija = NivoStudija::all();

    return view('students.index', compact('students', 'nivoStudija'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'ime' => 'required|string|max:255',
      'prezime' => 'required|string|max:255',
      'br_indexa' => 'required|string|max:255|unique:studenti,br_indexa',
      'datum_rodjenja' => 'required|date',
      'telefon' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:studenti,email',
      'godina_studija' => 'required|integer',
      'jmbg' => 'required|string|size:13|unique:studenti,jmbg',
      'nivo_studija_id' => 'required|exists:nivo_studija,id',
    ]);

    Student::create($validated);

    return redirect()->route('students.index')
      ->with('success', 'Student created successfully!');
  }

  public function update(Request $request, $id)
  {
    $student = Student::findOrFail($id);

    $validated = $request->validate([
      'ime' => 'required|string|max:255',
      'prezime' => 'required|string|max:255',
      'br_indexa' => 'required|string|max:255|unique:studenti,br_indexa,' . $id,
      'datum_rodjenja' => 'required|date',
      'telefon' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:studenti,email,' . $id,
      'godina_studija' => 'required|integer',
      'jmbg' => 'required|string|size:13|unique:studenti,jmbg,' . $id,
      'nivo_studija_id' => 'required|exists:nivo_studija,id',
    ]);

    $student->update($validated);

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

  public function bulkDelete(Request $request)
{
    $ids = $request->ids;
    if ($ids && is_array($ids)) {
        \App\Models\Student::whereIn('id', $ids)->delete();
        return redirect()->route('students.index')->with('success', 'Selected students deleted successfully.');
    }
    return redirect()->route('students.index')->with('error', 'No students selected.');
}


}
