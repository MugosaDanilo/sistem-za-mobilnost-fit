<?php

namespace Database\Factories;

use App\Models\Univerzitet;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniverzitetFactory extends Factory
{
    protected $model = Univerzitet::class;

    public function definition()
    {
        return [
            'naziv' => $this->faker->company,
            'drzava' => $this->faker->country,
            'grad' => $this->faker->city,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
