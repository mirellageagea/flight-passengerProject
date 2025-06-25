<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array

    {
      $departure = $this->faker->dateTimeBetween('now', '+30 days');
      $arrival = (clone $departure)->modify('+2 hours');

      return [
        'number' => $this->faker->regexify('[A-Z]{2}[0-9]{3,4}'),
        'departure_city' => $this->faker->city,
        'arrival_city' => $this->faker->city,
        'departure_time' => $departure,
        'arrival_time' => $arrival,
      ];
    }
}
