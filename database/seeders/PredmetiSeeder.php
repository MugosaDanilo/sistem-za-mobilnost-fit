<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use App\Models\Predmet;
use Illuminate\Database\Seeder;

use App\Services\SubjectImportService;

class PredmetiSeeder extends Seeder
{
    public function run(): void
    {
        $importer = new SubjectImportService();
        $fitPath = storage_path('app/predmeti/FIT_Nastavni_planovi_1.xlsx');

        $basic = NivoStudija::firstOrCreate(['naziv' => 'Osnovne'], ['naziv' => 'Osnovne']);
        $master = NivoStudija::firstOrCreate(['naziv' => 'Master'], ['naziv' => 'Master']);

        $unimed = Fakultet::where('naziv', 'LIKE', '%FIT%')
            ->orWhere('naziv', 'LIKE', '%Fakultet za informacione tehnologije%')
            ->first();

        if (!$unimed) {
            $unimed = Fakultet::firstOrCreate(
                ['naziv' => 'Fakultet za informacione tehnologije (FIT)'],
                ['naziv' => 'Fakultet za informacione tehnologije (FIT)']
            );
        }

        $coursesFitBasic = [];
        $coursesFitMaster = [];

        if (file_exists($fitPath)) {
            $coursesFitBasic = $importer->loadCoursesFit($fitPath, 'basic');
            $coursesFitMaster = $importer->loadCoursesFit($fitPath, 'master');
        }

        foreach ($coursesFitBasic as $c) {
            Predmet::updateOrCreate(
                [
                    'fakultet_id' => $unimed->id,
                    'sifra_predmeta' => $c['Sifra Predmeta'] ?? '',
                ],
                [
                    'naziv' => $c['Naziv Predmeta'] ?? '',
                    'naziv_engleski' => $c['Naziv Engleski'] ?? null,
                    'ects' => (int) ($c['ECTS'] ?? 0),
                    'semestar' => (int) ($c['Semestar'] ?? 1),
                    'nivo_studija_id' => $basic->id ?? null,
                ]
            );
        }

        foreach ($coursesFitMaster as $c) {
            Predmet::updateOrCreate(
                [
                    'fakultet_id' => $unimed->id,
                    'sifra_predmeta' => $c['Sifra Predmeta'] ?? '',
                ],
                [
                    'naziv' => $c['Naziv Predmeta'] ?? '',
                    'naziv_engleski' => $c['Naziv Engleski'] ?? null,
                    'ects' => (int) ($c['ECTS'] ?? 0),
                    'semestar' => (int) ($c['Semestar'] ?? 1),
                    'nivo_studija_id' => $master->id ?? null,
                ]
            );
        }

        $etfPath = storage_path('app/predmeti/etf_predmeti.xlsx');
        if (file_exists($etfPath)) {
            $coursesEtf = $importer->loadCoursesGeneric($etfPath);
            $etf = Fakultet::where('naziv', 'LIKE', '%ETF%')
                ->orWhere('naziv', 'LIKE', '%Elektrotehnički%')
                ->first();

            if (!$etf) {
                
                $etf = Fakultet::firstOrCreate(
                    ['naziv' => 'Elektrotehnički fakultet (ETF)'],
                    ['naziv' => 'Elektrotehnički fakultet (ETF)']
                );
            }

            foreach ($coursesEtf as $c) {
                Predmet::updateOrCreate(
                    [
                        'fakultet_id' => $etf->id,
                        'sifra_predmeta' => $c['Sifra Predmeta'] ?? '',
                    ],
                    [
                        'naziv' => $c['Naziv Predmeta'] ?? '',
                        'naziv_engleski' => null,
                        'ects' => (int) ($c['ECTS'] ?? 0),
                        'semestar' => (int) ($c['Semestar'] ?? 1),
                        'nivo_studija_id' => $basic->id ?? null,
                    ]
                );
            }
        }

        $swedenFaculty = Fakultet::firstOrCreate(
            ['naziv' => 'KTH Royal Institute of Technology (Sweden)'],
            ['naziv' => 'KTH Royal Institute of Technology (Sweden)']
        );

        $swedishSubjects = [
            
            ['code' => 'SE-CS-101', 'name' => 'Introduction to Programming', 'name_en' => 'Introduction to Programming', 'ects' => 7, 'semester' => 1, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-102', 'name' => 'Data Structures and Algorithms', 'name_en' => 'Data Structures and Algorithms', 'ects' => 7, 'semester' => 2, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-103', 'name' => 'Object-Oriented Programming', 'name_en' => 'Object-Oriented Programming', 'ects' => 7, 'semester' => 2, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-104', 'name' => 'Computer Networks', 'name_en' => 'Computer Networks', 'ects' => 7, 'semester' => 3, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-105', 'name' => 'Operating Systems', 'name_en' => 'Operating Systems', 'ects' => 7, 'semester' => 3, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-106', 'name' => 'Databases', 'name_en' => 'Databases', 'ects' => 7, 'semester' => 4, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-107', 'name' => 'Software Engineering', 'name_en' => 'Software Engineering', 'ects' => 7, 'semester' => 4, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-108', 'name' => 'Web Development', 'name_en' => 'Web Development', 'ects' => 7, 'semester' => 5, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-109', 'name' => 'Information Security', 'name_en' => 'Information Security', 'ects' => 7, 'semester' => 5, 'level' => 'Osnovne'],
            ['code' => 'SE-CS-110', 'name' => 'Cloud Computing', 'name_en' => 'Cloud Computing', 'ects' => 7, 'semester' => 6, 'level' => 'Osnovne'],

            ['code' => 'SE-ML-501', 'name' => 'Machine Learning', 'name_en' => 'Machine Learning', 'ects' => 7, 'semester' => 1, 'level' => 'Master'],
            ['code' => 'SE-ML-502', 'name' => 'Deep Learning', 'name_en' => 'Deep Learning', 'ects' => 7, 'semester' => 1, 'level' => 'Master'],
            ['code' => 'SE-ML-503', 'name' => 'Data Mining', 'name_en' => 'Data Mining', 'ects' => 7, 'semester' => 2, 'level' => 'Master'],
            ['code' => 'SE-ML-504', 'name' => 'Natural Language Processing', 'name_en' => 'Natural Language Processing', 'ects' => 7, 'semester' => 2, 'level' => 'Master'],
            ['code' => 'SE-ML-505', 'name' => 'Reinforcement Learning', 'name_en' => 'Reinforcement Learning', 'ects' => 7, 'semester' => 2, 'level' => 'Master'],

            ['code' => 'SE-BIZ-201', 'name' => 'Project Management', 'name_en' => 'Project Management', 'ects' => 7, 'semester' => 3, 'level' => 'Osnovne'],
            ['code' => 'SE-BIZ-202', 'name' => 'Entrepreneurship and Innovation', 'name_en' => 'Entrepreneurship and Innovation', 'ects' => 7, 'semester' => 4, 'level' => 'Osnovne'],
            ['code' => 'SE-BIZ-203', 'name' => 'Digital Marketing', 'name_en' => 'Digital Marketing', 'ects' => 7, 'semester' => 5, 'level' => 'Osnovne'],

            ['code' => 'SE-LANG-101', 'name' => 'Swedish Language Basics', 'name_en' => 'Swedish Language Basics', 'ects' => 5, 'semester' => 1, 'level' => 'Osnovne'],
            ['code' => 'SE-LANG-201', 'name' => 'Academic English', 'name_en' => 'Academic English', 'ects' => 5, 'semester' => 2, 'level' => 'Osnovne'],
        ];

        foreach ($swedishSubjects as $s) {
            $levelModel = ($s['level'] === 'Master') ? $master : $basic;

            Predmet::updateOrCreate(
                [
                    'fakultet_id' => $swedenFaculty->id,
                    'sifra_predmeta' => $s['code'],
                ],
                [
                    'naziv' => $s['name'],
                    'naziv_engleski' => $s['name_en'],
                    'ects' => (int) $s['ects'],
                    'semestar' => (int) $s['semester'],
                    'nivo_studija_id' => $levelModel->id ?? null,
                ]
            );
        }
    }
}
