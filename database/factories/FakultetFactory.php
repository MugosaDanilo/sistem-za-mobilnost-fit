<?php

namespace Database\Factories;

use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakultetFactory extends Factory
{
   

    protected $model = Fakultet::class;

    public function definition()
    {
        return [
            'naziv' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefon' => $this->faker->phoneNumber(),
            'web' => $this->faker->optional()->url(),
            'uputstvo_za_ocjene' => $this->faker->optional()->paragraph(),
            'univerzitet_id' => Univerzitet::factory(), // stvara univerzitet automatski
        ];
    }
}
