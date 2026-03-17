<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FootballMatch>
 */
class FootballMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organizer_id' => User::factory(),
            'description' => $this->faker->sentence(),
            'starts_at' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'duration' => $this->faker->randomElement([60, 90, 120]),
            'match_type' => $this->faker->randomElement(['5v5', '7v7', '11v11']),
            'max_players' => $this->faker->randomElement([10, 14, 22]),
            'required_level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'price' => $this->faker->randomFloat(2, 0, 20),
            'location_name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'status' => 'open',
        ];
    }
}
