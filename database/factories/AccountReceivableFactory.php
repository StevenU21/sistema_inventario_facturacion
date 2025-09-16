<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountReceivable>
 */
class AccountReceivableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount_due' => fake()->randomFloat(2, 20, 1000),
            'amount_paid' => 0,
            'status' => 'pending',
            'entity_id' => null,
            'sale_id' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'amount_paid' => $attributes['amount_due'] ?? 0,
                'status' => 'paid',
            ];
        });
    }
}
