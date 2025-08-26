<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'ruc' => fake()->unique()->numerify('###########'),
            'logo' => null,
            'description' => fake()->sentence(),
            'address' => fake()->address(),
            'phone' => fake()->unique()->phoneNumber(),
            'email' => fake()->unique()->safeEmail()
        ];
    }
}
