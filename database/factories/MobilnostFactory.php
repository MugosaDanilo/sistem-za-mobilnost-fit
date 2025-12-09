<?php

namespace Database\Factories;

use App\Models\Fakultet;
use App\Models\Mobilnost;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class MobilnostFactory extends Factory
{
    protected $model = Mobilnost::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 year', 'now');
        $end = (clone $start)->modify('+3 months');

        return [
            'datum_pocetka' => $start->format('Y-m-d'),
            'datum_kraja' => $end->format('Y-m-d'),
            'student_id' => Student::factory(),
            'fakultet_id' => Fakultet::factory(),
        ];
    }
}

