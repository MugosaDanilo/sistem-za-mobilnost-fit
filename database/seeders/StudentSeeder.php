<?php

namespace Database\Seeders;

use App\Models\NivoStudija;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $osnovne = NivoStudija::where('naziv', 'Osnovne')->first();
        $master = NivoStudija::where('naziv', 'Master')->first();
        $fit = \App\Models\Fakultet::where('naziv', 'FIT')->first();

        $students = [
            ['ime'=>'Danilo','prezime'=>'Mugosa','br_indexa'=>'52-23','telefon'=>'061111111','email'=>'danilomugosa@example.com','godina_studija'=>3,'jmbg'=>'1234567890121','pol'=>'musko'],
            ['ime'=>'Emir','prezime'=>'Muhovic','br_indexa'=>'09-23','telefon'=>'061111111','email'=>'emirmuhovic@example.com','godina_studija'=>3,'jmbg'=>'1234567890122','pol'=>'musko'],
            ['ime'=>'Luka','prezime'=>'Vujacic','br_indexa'=>'109-22','telefon'=>'061111111','email'=>'lukavujacic@example.com','godina_studija'=>3,'jmbg'=>'1234567890123','pol'=>'musko'],
            ['ime'=>'Luka','prezime'=>'Vojinovic','br_indexa'=>'43-23','telefon'=>'061111111','email'=>'lukavojinovic@example.com','godina_studija'=>3,'jmbg'=>'1234567890124','pol'=>'musko'],
            ['ime'=>'Sanja','prezime'=>'Radulovic','br_indexa'=>'32-16','telefon'=>'061111111','email'=>'sanjaradulovic@example.com','godina_studija'=>3,'jmbg'=>'1234567890125','pol'=>'zensko'],
            ['ime'=>'Damjan','prezime'=>'Latinovic','br_indexa'=>'100-23','telefon'=>'061111111','email'=>'damjanlatinovic@example.com','godina_studija'=>3,'jmbg'=>'1234567890126','pol'=>'musko'],
            ['ime'=>'Vlado','prezime'=>'Vojinovic','br_indexa'=>'22-22','telefon'=>'061111111','email'=>'vladovojinovic@example.com','godina_studija'=>3,'jmbg'=>'1234567890127','pol'=>'musko'],
            ['ime'=>'Marko','prezime'=>'Markovic','br_indexa'=>'22-20','telefon'=>'061111111','email'=>'markomarkovic@example.com','godina_studija'=>2,'jmbg'=>'1234567890128','pol'=>'musko'],
            ['ime'=>'Janko','prezime'=>'Jankovic','br_indexa'=>'22-19','telefon'=>'061111111','email'=>'jankojankovic@example.com','godina_studija'=>2,'jmbg'=>'1234567890129','pol'=>'musko'],

            ['ime'=>'Danilo','prezime'=>'Vujovic','br_indexa'=>'97-24','telefon'=>'061111111','email'=>'danilo.vujovic.9724@example.com','godina_studija'=>1,'jmbg'=>'2234567890200','pol'=>'musko'],
            ['ime'=>'Stasa','prezime'=>'Jovanovic','br_indexa'=>'03-23','telefon'=>'061111111','email'=>'stasa.jovanovic.0323@example.com','godina_studija'=>2,'jmbg'=>'2234567890201','pol'=>'zensko'],
            ['ime'=>'Adrian','prezime'=>'Adrovic','br_indexa'=>'08-23','telefon'=>'061111111','email'=>'adrian.adrovic.0823@example.com','godina_studija'=>2,'jmbg'=>'2234567890202','pol'=>'musko'],
            ['ime'=>'Irena','prezime'=>'Kandic','br_indexa'=>'10-23','telefon'=>'061111111','email'=>'irena.kandic.1023@example.com','godina_studija'=>2,'jmbg'=>'2234567890203','pol'=>'zensko'],
            ['ime'=>'Amer','prezime'=>'Hot','br_indexa'=>'17-23','telefon'=>'061111111','email'=>'amer.hot.1723@example.com','godina_studija'=>2,'jmbg'=>'2234567890204','pol'=>'musko'],
            ['ime'=>'Lazar','prezime'=>'Marinovic','br_indexa'=>'22-23','telefon'=>'061111111','email'=>'lazar.marinovic.2223@example.com','godina_studija'=>2,'jmbg'=>'2234567890205','pol'=>'musko'],
            ['ime'=>'Milija','prezime'=>'Knezevic','br_indexa'=>'47-23','telefon'=>'061111111','email'=>'milija.knezevic.4723@example.com','godina_studija'=>2,'jmbg'=>'2234567890206','pol'=>'musko'],
            ['ime'=>'Filip','prezime'=>'Cokovski','br_indexa'=>'53-23','telefon'=>'061111111','email'=>'filip.cokovski.5323@example.com','godina_studija'=>2,'jmbg'=>'2234567890207','pol'=>'musko'],
            ['ime'=>'Mia','prezime'=>'Vranes','br_indexa'=>'54-23','telefon'=>'061111111','email'=>'mia.vranes.5423@example.com','godina_studija'=>2,'jmbg'=>'2234567890208','pol'=>'zensko'],
            ['ime'=>'Dmitrii','prezime'=>'Bespalov','br_indexa'=>'59-23','telefon'=>'061111111','email'=>'dmitrii.bespalov.5923@example.com','godina_studija'=>2,'jmbg'=>'2234567890209','pol'=>'musko'],
            ['ime'=>'Damjan','prezime'=>'Vujicic','br_indexa'=>'62-23','telefon'=>'061111111','email'=>'damjan.vujicic.6223@example.com','godina_studija'=>2,'jmbg'=>'2234567890210','pol'=>'musko'],
            ['ime'=>'Luka','prezime'=>'Dragicevic','br_indexa'=>'85-23','telefon'=>'061111111','email'=>'luka.dragicevic.8523@example.com','godina_studija'=>2,'jmbg'=>'2234567890211','pol'=>'musko'],
            ['ime'=>'Ajsa','prezime'=>'Dacic','br_indexa'=>'01-23','telefon'=>'061111111','email'=>'ajsa.dacic.0123@example.com','godina_studija'=>2,'jmbg'=>'2234567890212','pol'=>'zensko'],
            ['ime'=>'Aleksa','prezime'=>'Bojovic','br_indexa'=>'30-22','telefon'=>'061111111','email'=>'aleksa.bojovic.3022@example.com','godina_studija'=>3,'jmbg'=>'2234567890213','pol'=>'musko'],
            ['ime'=>'Lazar','prezime'=>'Krsmanovic','br_indexa'=>'37-22','telefon'=>'061111111','email'=>'lazar.krsmanovic.3722@example.com','godina_studija'=>3,'jmbg'=>'2234567890214','pol'=>'musko'],
            ['ime'=>'Anel','prezime'=>'Ramovic','br_indexa'=>'95-22','telefon'=>'061111111','email'=>'anel.ramovic.9522@example.com','godina_studija'=>3,'jmbg'=>'2234567890215','pol'=>'musko'],
            ['ime'=>'Mhill','prezime'=>'Camaj','br_indexa'=>'67-21','telefon'=>'061111111','email'=>'mhill.camaj.6721@example.com','godina_studija'=>4,'jmbg'=>'2234567890216','pol'=>'musko'],
            ['ime'=>'Milos','prezime'=>'Kovacevic','br_indexa'=>'35-20','telefon'=>'061111111','email'=>'milos.kovacevic.3520@example.com','godina_studija'=>4,'jmbg'=>'2234567890217','pol'=>'musko'],
            ['ime'=>'Nemanja','prezime'=>'Krstic','br_indexa'=>'45-20','telefon'=>'061111111','email'=>'nemanja.krstic.4520@example.com','godina_studija'=>4,'jmbg'=>'2234567890218','pol'=>'musko'],
            ['ime'=>'Momcilo','prezime'=>'Kovacevic','br_indexa'=>'31-22','telefon'=>'061111111','email'=>'momcilo.kovacevic.3122@example.com','godina_studija'=>3,'jmbg'=>'2234567890219','pol'=>'musko'],
            ['ime'=>'Milena','prezime'=>'Bigovic','br_indexa'=>'37-23','telefon'=>'061111111','email'=>'milena.bigovic.3723@example.com','godina_studija'=>2,'jmbg'=>'2234567890220','pol'=>'zensko'],
            ['ime'=>'Kenan','prezime'=>'Bakija','br_indexa'=>'42-21','telefon'=>'061111111','email'=>'kenan.bakija.4221@example.com','godina_studija'=>4,'jmbg'=>'2234567890221','pol'=>'musko'],
            ['ime'=>'Nemanja','prezime'=>'Golubovic','br_indexa'=>'88-23','telefon'=>'061111111','email'=>'nemanja.golubovic.8823@example.com','godina_studija'=>2,'jmbg'=>'2234567890222','pol'=>'musko'],
            ['ime'=>'Andrija','prezime'=>'Velickovic','br_indexa'=>'Jun-23','telefon'=>'061111111','email'=>'andrija.velickovic.jun23@example.com','godina_studija'=>2,'jmbg'=>'2234567890223','pol'=>'musko'],
        ];

        foreach ($students as $studentData) {
            $suffix = null;
            if (preg_match('/-(\d{2})$/', $studentData['br_indexa'], $m)) {
                $suffix = (int) $m[1];
            }

            $level = $osnovne;
            if ($master && $studentData['godina_studija'] >= 4 && $suffix !== null && $suffix <= 20) {
                $level = $master;
            }

            if ($suffix !== null && $suffix <= 20 && $master && $studentData['godina_studija'] === 4) {
                $level = $master;
            }

            $seed = crc32($studentData['br_indexa'] . '|' . $studentData['email']);
            $year = 1998 + ($seed % 7);
            $month = 1 + (($seed >> 3) % 12);
            $day = 1 + (($seed >> 7) % 28);
            $studentData['datum_rodjenja'] = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $studentData['nivo_studija_id'] = $level?->id;

            $student = Student::updateOrCreate(
                ['br_indexa' => $studentData['br_indexa']],
                $studentData
            );

            if ($fit) {
                $student->fakulteti()->syncWithoutDetaching([$fit->id]);
            }
        }
    }
}
