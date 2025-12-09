<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakultet;
use PhpOffice\PhpWord\IOFactory;

class TooltipController extends Controller
{
    public function index()
    {
        $fakulteti = Fakultet::all();
        return view('tooltip.index', compact('fakulteti'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:fakulteti,id',
            'tooltip_file' => 'required|mimes:txt,docx',
        ]);

        $faculty = Fakultet::findOrFail($request->faculty_id);
        $file = $request->file('tooltip_file');
        $content = '';

        // Čitanje fajla
        if ($file->getClientOriginalExtension() === 'txt') {
            $content = file_get_contents($file->getRealPath());
        } else {
            // Čitanje DOCX fajla
            $phpWord = IOFactory::load($file->getRealPath());
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $content .= $element->getText() . "\n";
                    }
                }
            }
        }

        // Ako već postoji tooltip, traži potvrdu overwrite
        if ($faculty->uputstvo_za_ocjene) {
            return redirect()->route('tooltip.index')
                ->with('confirm_overwrite', true)
                ->with('faculty_id', $faculty->id)
                ->with('new_text', $content);
        }

        $faculty->update(['uputstvo_za_ocjene' => $content]);

        return redirect()->route('tooltip.index')->with('success', 'Tooltip uploaded successfully!');
    }

    public function overwrite(Request $request)
    {
        $faculty = Fakultet::findOrFail($request->faculty_id);
        $faculty->update(['uputstvo_za_ocjene' => $request->new_text]);

        return redirect()->route('tooltip.index')->with('success', 'Tooltip overwritten successfully!');
    }
}
