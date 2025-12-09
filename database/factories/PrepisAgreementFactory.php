<?php

namespace Database\Factories;

use App\Models\Predmet;
use App\Models\Prepis;
use App\Models\PrepisAgreement;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrepisAgreementFactory extends Factory
{
    protected $model = PrepisAgreement::class;

    public function definition(): array
    {
        return [
            'status' => 'u procesu',
            'prepis_id' => Prepis::factory(),
            'fit_predmet_id' => Predmet::factory(),
            'strani_predmet_id' => Predmet::factory(),
        ];
    }
}

