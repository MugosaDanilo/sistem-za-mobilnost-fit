<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Mobilnost;
use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Http\Request;

class IzvjestajController extends Controller
{
    /**
     * Prikaži sve dostupne izvještaje
     */
    public function index()
    {
        return view('izvjestaji.index');
    }

    /**
     * Prikaži formu za izbor tipa izvještaja
     */
    public function create()
    {
        return view('izvjestaji.create');
    }

    /**
     * Generiši izvještaj na osnovu parametara
     */
    public function store(Request $request)
    {
        $tip = $request->get('tip', 'studenti');
        
        return redirect()->route('izvjestaji.show', ['id' => $tip]);
    }

    /**
     * Prikaži izvještaj sa podacima
     */
    public function show($id)
    {
        $data = [];
        
        switch ($id) {
            case 'studenti':
                $data = $this->getStudentiReport();
                break;
            case 'mobilnosti':
                $data = $this->getMobilnostiReport();
                break;
            case 'fakulteti':
                $data = $this->getFakultetiReport();
                break;
            case 'univerziteti':
                $data = $this->getUniverzitetiReport();
                break;
            default:
                $data = [];
        }
        
        return view('izvjestaji.show', compact('data', 'id'));
    }

    /**
     * Prikaži formu za uređivanje
     */
    public function edit($id)
    {
        return view('izvjestaji.edit');
    }

    /**
     * Ažurira izvještaj
     */
    public function update(Request $request, $id)
    {
        // Ako je potrebna logika ažuriranja
        return redirect()->route('izvjestaji.index')->with('success', 'Izvještaj je ažuriran');
    }

    /**
     * Briši izvještaj
     */
    public function destroy($id)
    {
        // Ako je potrebna logika brisanja
        return redirect()->route('izvjestaji.index')->with('success', 'Izvještaj je obrisan');
    }

    /**
     * Generiši izvještaj o studentima
     */
    private function getStudentiReport()
    {
        $studenti = Student::with('nivoStudija')->get();
        
        return [
            'naslov' => 'Izvještaj o Studentima',
            'tip' => 'studenti',
            'kolone' => ['ID', 'Ime', 'Prezime', 'Broj Indexa', 'Email', 'Nivo Studija'],
            'podaci' => $studenti->map(function ($student) {
                return [
                    $student->id,
                    $student->ime,
                    $student->prezime,
                    $student->br_indexa,
                    $student->email,
                    $student->nivoStudija->naziv ?? 'N/A',
                ];
            })->toArray(),
            'ukupno' => $studenti->count(),
        ];
    }

    /**
     * Generiši izvještaj o mobilnostima
     */
    private function getMobilnostiReport()
    {
        $mobilnosti = Mobilnost::with(['student', 'fakultet'])->get();
        
        return [
            'naslov' => 'Izvještaj o Mobilnostima',
            'tip' => 'mobilnosti',
            'kolone' => ['ID', 'Student', 'Fakultet', 'Datum Početka', 'Datum Kraja'],
            'podaci' => $mobilnosti->map(function ($mobilnost) {
                return [
                    $mobilnost->id,
                    $mobilnost->student->ime . ' ' . $mobilnost->student->prezime,
                    $mobilnost->fakultet->naziv ?? 'N/A',
                    $mobilnost->datum_pocetka,
                    $mobilnost->datum_kraja,
                ];
            })->toArray(),
            'ukupno' => $mobilnosti->count(),
        ];
    }

    /**
     * Generiši izvještaj o fakultetima
     */
    private function getFakultetiReport()
    {
        $fakulteti = Fakultet::with('univerzitet')->get();
        
        return [
            'naslov' => 'Izvještaj o Fakultetima',
            'tip' => 'fakulteti',
            'kolone' => ['ID', 'Naziv', 'Univerzitet'],
            'podaci' => $fakulteti->map(function ($fakultet) {
                return [
                    $fakultet->id,
                    $fakultet->naziv,
                    $fakultet->univerzitet->naziv ?? 'N/A',
                ];
            })->toArray(),
            'ukupno' => $fakulteti->count(),
        ];
    }

    /**
     * Generiši izvještaj o univerzitetima
     */
    private function getUniverzitetiReport()
    {
        $univerziteti = Univerzitet::withCount('fakulteti')->get();
        
        return [
            'naslov' => 'Izvještaj o Univerzitetima',
            'tip' => 'univerziteti',
            'kolone' => ['ID', 'Naziv', 'Broj Fakulteta'],
            'podaci' => $univerziteti->map(function ($univerzitet) {
                return [
                    $univerzitet->id,
                    $univerzitet->naziv,
                    $univerzitet->fakulteti_count,
                ];
            })->toArray(),
            'ukupno' => $univerziteti->count(),
        ];
    }
}
