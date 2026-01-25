<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\Mobilnost;
use App\Models\Prepis;
use Illuminate\Database\Seeder;

class PrepisSeeder extends Seeder
{
    public function run(): void
    {
        $mobilnosti = Mobilnost::all();
        if ($mobilnosti->isEmpty()) {
            return;
        }

        $statusi = ['u procesu', 'odobren', 'odbijen'];

        foreach ($mobilnosti as $i => $m) {
            Prepis::updateOrCreate(
                [
                    'student_id' => $m->student_id,
                    'fakultet_id' => $m->fakultet_id,
                ],
                [
                    'datum' => $m->datum_kraja,
                    'status' => $statusi[$i % count($statusi)],
                    'napomena' => ($i % 3 === 0) ? 'Prepis kreiran na osnovu mobilnosti' : null,
                ]
            );
        }
    }
}
