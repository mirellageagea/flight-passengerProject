<?php

namespace Database\Factories;

use App\Models\Flight;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Passenger>
 */
class PassengerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'dob' => $this->faker->date('Y-m-d', '2005-01-01'),
            'passport_expiry_date' => $this->faker->dateTimeBetween('+1 year', '+10 years'),
            'flight_id' => Flight::inRandomOrder()->first()?->id ?? Flight::factory(),        
        ];
    }
}
