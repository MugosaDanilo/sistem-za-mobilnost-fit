<?php

namespace Database\Seeders;

use App\Models\NivoStudija;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $osnovne = NivoStudija::where('naziv', 'Osnovne')->first();
        $master = NivoStudija::where('naziv', 'Master')->first();

        Student::create([
            'ime' => 'Marko',
            'prezime' => 'Marković',
            'br_indexa' => 'IB12345',
            'datum_rodjenja' => '2000-12-12',
            'telefon' => '061111111',
            'email' => 'marko@example.com',
            'godina_studija' => 3,
            'jmbg' => '1234567890123',
            'nivo_studija_id' => $osnovne->id,
        ]);

        Student::create([
            'ime' => 'Ana',
            'prezime' => 'Ilić',
            'br_indexa' => 'IM54321',
            'datum_rodjenja' => '1998-06-20',
            'telefon' => '061222222',
            'email' => 'ana@example.com',
            'godina_studija' => 2,
            'jmbg' => '9876543210987',
            'nivo_studija_id' => $master->id,
        ]);
    }
}
