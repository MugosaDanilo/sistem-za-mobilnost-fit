<?php

namespace Database\Factories;

use App\Models\LearningAgreement;
use App\Models\Mobilnost;
use App\Models\Predmet;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearningAgreementFactory extends Factory
{
    protected $model = LearningAgreement::class;

    public function definition(): array
    {
        return [
            'mobilnost_id' => Mobilnost::factory(),
            'fit_predmet_id' => Predmet::factory(),
            'strani_predmet_id' => Predmet::factory(),
            'napomena' => null,
            'ocjena' => null,
        ];
    }
}

