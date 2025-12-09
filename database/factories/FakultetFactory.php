<?php

namespace Database\Factories;

use App\Models\Fakultet;
use App\Models\Univerzitet;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakultetFactory extends Factory
{
    protected $model = Fakultet::class;

    public function definition(): array
    {
        return [
            'naziv' => $this->faker->company . ' Fakultet',
            'email' => $this->faker->unique()->safeEmail(),
            'telefon' => $this->faker->phoneNumber(),
            'web' => $this->faker->url(),
            'uputstvo_za_ocjene' => null,
            'univerzitet_id' => Univerzitet::factory(), // ✅ više nije NULL
        ];
    }
}
