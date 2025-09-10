<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Almacén ' . fake()->unique()->citySuffix(),
            'address' => fake()->address(),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
