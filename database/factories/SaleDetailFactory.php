<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleDetail>
 */
class SaleDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unit_price = fake()->randomFloat(2, 10, 500);
        $quantity = fake()->numberBetween(1, 5);
        $sub_total = round($unit_price * $quantity, 2);
        return [
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'sub_total' => $sub_total,
            'discount' => null,
            'discount_amount' => null,
            // These will be set explicitly in the SaleFactory afterCreating
            'product_variant_id' => null,
            'sale_id' => null,
        ];
    }
}
