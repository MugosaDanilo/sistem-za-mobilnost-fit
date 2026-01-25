<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\LearningAgreement;
use App\Models\Prepis;
use App\Models\PrepisAgreement;
use Illuminate\Database\Seeder;

class PrepisAgreementSeeder extends Seeder
{
    public function run(): void
    {
        $fit = Fakultet::where('naziv', 'FIT')->first();
        if (!$fit) {
            return;
        }

        $prepisi = Prepis::all();
        if ($prepisi->isEmpty()) {
            return;
        }

        $statusi = ['u procesu', 'odobren', 'odbijen'];

        foreach ($prepisi as $i => $prepis) {
            $agreements = LearningAgreement::whereHas('mobilnost', function ($q) use ($prepis) {
                $q->where('student_id', $prepis->student_id)
                  ->where('fakultet_id', $prepis->fakultet_id);
            })->get();

            if ($agreements->isEmpty()) {
                continue;
            }

            $count = min(3, $agreements->count());

            for ($k = 0; $k < $count; $k++) {
                $la = $agreements[$k];

                PrepisAgreement::updateOrCreate(
                    [
                        'prepis_id' => $prepis->id,
                        'fit_predmet_id' => $la->fit_predmet_id,
                        'strani_predmet_id' => $la->strani_predmet_id,
                    ],
                    [
                        'status' => $statusi[($i + $k) % count($statusi)],
                    ]
                );
            }
        }
    }
}
