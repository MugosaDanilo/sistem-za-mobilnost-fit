<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FakultetController extends Controller
{
    public function index()
    {
        $faculties = Fakultet::with('univerzitet')->get();
        $universities = Univerzitet::all();

        return view('faculty.index', compact('faculties', 'universities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:fakulteti,email',
            'telefon' => 'required|string|max:255',
            'web' => 'nullable|string|max:255',
            'uputstvo_za_ocjene' => 'required|file|mimes:doc,docx,txt|max:2048',
            'univerzitet_id' => 'required|exists:univerziteti,id',
        ]);

        if ($request->hasFile('uputstvo_za_ocjene')) {
            $file = $request->file('uputstvo_za_ocjene');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/faculty_instructions', $filename);
            $validated['uputstvo_za_ocjene'] = $filename;
        }

        Fakultet::create($validated);

        return redirect()->back()->with('success', 'Faculty added successfully!');
    }

    public function update(Request $request, $id)
    {
        $faculty = Fakultet::findOrFail($id);

        $validated = $request->validate([
            'naziv' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('fakulteti')->ignore($faculty->id)],
            'telefon' => 'required|string|max:255',
            'web' => 'nullable|string|max:255',
            'uputstvo_za_ocjene' => 'nullable|file|mimes:doc,docx,txt|max:2048',
            'univerzitet_id' => 'required|exists:univerziteti,id',
        ]);

        if ($request->hasFile('uputstvo_za_ocjene')) {
            if ($faculty->uputstvo_za_ocjene && Storage::exists('public/faculty_instructions/' . $faculty->uputstvo_za_ocjene)) {
                Storage::delete('public/faculty_instructions/' . $faculty->uputstvo_za_ocjene);
            }
            $file = $request->file('uputstvo_za_ocjene');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/faculty_instructions', $filename);
            $validated['uputstvo_za_ocjene'] = $filename;
        }

        $faculty->update($validated);

        return redirect()->route('faculties.index')->with('success', 'Faculty updated successfully!');
    }

    public function destroy($id)
    {
        $faculty = Fakultet::findOrFail($id);

        if ($faculty->uputstvo_za_ocjene && Storage::exists('public/faculty_instructions/' . $faculty->uputstvo_za_ocjene)) {
            Storage::delete('public/faculty_instructions/' . $faculty->uputstvo_za_ocjene);
        }

        $faculty->delete();

        return redirect()->route('faculties.index')->with('success', 'Faculty deleted successfully!');
    }
}
