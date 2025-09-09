<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->serialNumber(),
            'subtotal' => null,
            'total' => null,
            'entity_id' => EntityFactory::inRandomOrder()->first()->id,
            'warehouse_id' => WarehouseFactory::inRandomOrder()->first()->id,
            'user_id' => UserFactory::inRandomOrder()->first()->id,
            'payment_method_id' => PaymentMethodFactory::inRandomOrder()->first()->id,
        ];
    }
}
