<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentDocumentController extends Controller
{
    public function index($studentId)
    {
        $student = Student::with('documents')->findOrFail($studentId);

        return view('students.documents', compact('student'));
    }

    public function store(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('file')->store('student_documents', 'public');

        StudentDocument::create([
            'student_id' => $student->id,
            'naziv' => $validated['naziv'],
            'file_path' => $path,
        ]);

        return redirect()->route('students.documents.index', $student->id)
            ->with('success', 'Dokument je uspješno uploadovan.');
    }

    public function download($id)
    {
        $document = StudentDocument::findOrFail($id);

        return Storage::disk('public')->download($document->file_path);
    }

    public function destroy($id)
    {
        $document = StudentDocument::findOrFail($id);
        $studentId = $document->student_id;

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('students.documents.index', $studentId)
            ->with('success', 'Dokument je uspješno obrisan.');
    }
}