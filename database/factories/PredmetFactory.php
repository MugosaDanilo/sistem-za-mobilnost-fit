<?php

namespace Database\Factories;

use App\Models\Fakultet;
use App\Models\NivoStudija;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Predmet>
 */
class PredmetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sifra_predmeta' => $this->faker->unique()->bothify('???###'),
            'naziv' => $this->faker->words(3, true),
            'semestar' => $this->faker->numberBetween(1, 8),
            'ects' => $this->faker->randomElement([3, 5, 6, 7, 8]),
            'fakultet_id' => Fakultet::factory(),
            'nivo_studija_id' => NivoStudija::factory(),
        ];
    }
}
