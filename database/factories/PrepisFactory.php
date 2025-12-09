<?php

namespace Database\Factories;

use App\Models\Fakultet;
use App\Models\Prepis;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrepisFactory extends Factory
{
    protected $model = Prepis::class;

    public function definition(): array
    {
        return [
            'datum' => $this->faker->date(),
            'status' => 'u procesu',
            'napomena' => null,
            'fakultet_id' => Fakultet::factory(),
            'student_id' => Student::factory(),
        ];
    }
}
