<?php

namespace Database\Factories;

use App\Models\NastavnaLista;
use App\Models\Predmet;
use Illuminate\Database\Eloquent\Factories\Factory;

class NastavnaListaFactory extends Factory
{
    protected $model = NastavnaLista::class;

    public function definition()
    {
        return [
            'predmet_id' => Predmet::factory(),
            'fakultet_id' => 1,
            'link' => $this->faker->url(),
        ];
    }
}
