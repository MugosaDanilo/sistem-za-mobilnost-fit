<?php

namespace Database\Factories;

use App\Models\Predmet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PredmetFactory extends Factory
{
    protected $model = Predmet::class;

    public function definition()
    {
        return [
            'naziv' => $this->faker->words(3, true),
            'ects' => $this->faker->numberBetween(3, 10),
            'fakultet_id' => 1, // ili možeš random()
            'semestar' => $this->faker->numberBetween(1, 8), 
            'sifra_predmeta' => strtoupper($this->faker->bothify('??###')),
        ];
    }
}
