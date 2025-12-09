<?php

namespace Database\Factories;

use App\Models\NivoStudija;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'ime' => $this->faker->firstName(),
            'prezime' => $this->faker->lastName(),
            'br_indexa' => 'IB' . $this->faker->numberBetween(100, 999) . '/' . $this->faker->numberBetween(2020, 2025),
            'datum_rodjenja' => $this->faker->date(),
            'telefon' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'godina_studija' => $this->faker->numberBetween(1, 4),
            'jmbg' => (string) $this->faker->numberBetween(1000000000000, 9999999999999),
            'nivo_studija_id' => NivoStudija::factory(), // ✅ više nije NULL
        ];
    }
}
