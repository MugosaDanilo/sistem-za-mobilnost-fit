<?php

namespace Database\Seeders;

use App\Models\Fakultet;
use App\Models\LearningAgreement;
use App\Models\Mobilnost;
use App\Models\Predmet;
use Illuminate\Database\Seeder;

class LearningAgreementSeeder extends Seeder
{
    public function run(): void
    {
        $fit = Fakultet::where('naziv', 'FIT')->first();
        if (!$fit) {
            return;
        }

        $fitPredmeti = Predmet::where('fakultet_id', $fit->id)->get();
        $straniPredmeti = Predmet::where('fakultet_id', '!=', $fit->id)->get();

        if ($fitPredmeti->isEmpty() || $straniPredmeti->isEmpty()) {
            return;
        }

        $mobilnosti = Mobilnost::all();
        if ($mobilnosti->isEmpty()) {
            return;
        }

        $ocjene = ['6', '7', '8', '9', '10', null];

        foreach ($mobilnosti as $i => $m) {
            $count = 3 + ($i % 3);

            for ($k = 0; $k < $count; $k++) {
                $fitP = $fitPredmeti[($i * 7 + $k * 3) % $fitPredmeti->count()];
                $strP = $straniPredmeti[($i * 11 + $k * 5) % $straniPredmeti->count()];
                $ocjena = $ocjene[($i + $k) % count($ocjene)];

                LearningAgreement::updateOrCreate(
                    [
                        'mobilnost_id' => $m->id,
                        'fit_predmet_id' => $fitP->id,
                        'strani_predmet_id' => $strP->id,
                    ],
                    [
                        'napomena' => $k === 0 ? 'Razmjena - inicijalni LA' : null,
                        'ocjena' => $ocjena,
                    ]
                );
            }
        }
    }
}
