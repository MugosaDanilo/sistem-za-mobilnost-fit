<?php

namespace Database\Factories;

use App\Models\NivoStudija;
use Illuminate\Database\Eloquent\Factories\Factory;

class NivoStudijaFactory extends Factory
{
    protected $model = NivoStudija::class;

    public function definition(): array
    {
        return [
            'naziv' => $this->faker->randomElement([
                'Osnovne studije',
                'Master studije',
                'Doktorske studije',
            ]),
        ];
    }
}
