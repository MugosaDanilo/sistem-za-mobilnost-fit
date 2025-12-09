<?php

namespace Database\Factories;

use App\Models\Univerzitet;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniverzitetFactory extends Factory
{
    protected $model = Univerzitet::class;

    public function definition(): array
    {
        return [
            'naziv' => $this->faker->company . ' Univerzitet',
            'drzava' => $this->faker->country(),
            'grad' => $this->faker->city(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}
