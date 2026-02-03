<?php

namespace Database\Factories;

use App\Models\MappingRequest;
use App\Models\Predmet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MappingRequestSubject>
 */
class MappingRequestSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mapping_request_id' => MappingRequest::factory(),
            'strani_predmet_id' => Predmet::factory(),
            'fit_predmet_id' => $this->faker->optional(0.7)->randomElement([Predmet::factory(), null]),
            'professor_id' => User::factory(),
            'is_rejected' => $this->faker->boolean(10),
        ];
    }
}
