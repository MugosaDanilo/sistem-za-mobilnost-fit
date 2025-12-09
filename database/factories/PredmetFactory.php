<?php

namespace Database\Factories;

use App\Models\Fakultet;
use App\Models\Predmet;
use Illuminate\Database\Eloquent\Factories\Factory;

class PredmetFactory extends Factory
{
    protected $model = Predmet::class;

    public function definition(): array
    {
        return [
            'naziv' => 'Predmet ' . $this->faker->word(),
            'ects' => $this->faker->numberBetween(3, 10),
            'semestar' => $this->faker->numberBetween(1, 8),
            'fakultet_id' => Fakultet::factory(),
        ];
    }
}
