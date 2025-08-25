<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'avatar' => null,
            'phone' => fake()->phoneNumber(),
            'identity_card' => fake()->unique()->numberBetween(10000000, 99999999),
            'gender' => fake()->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'user_id' => null,
        ];
    }
}
