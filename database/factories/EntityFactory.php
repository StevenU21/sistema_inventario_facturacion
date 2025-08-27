<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity>
 */
class EntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $is_client = fake()->boolean(70);
        $is_supplier = $is_client ? fake()->boolean(30) : true;

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'identity_card' => fake()->unique()->safeEmail(),
            'ruc' => fake()->unique()->numerify('###-###-###'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'description' => fake()->sentence(),
            'is_client' => $is_client,
            'is_supplier' => $is_supplier,
            'is_active' => fake()->boolean(90)
        ];
    }
}
