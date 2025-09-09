<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock' => fake()->numberBetween(0, 100),
            'min_stock' => fake()->numberBetween(0, 20),
            'purchase_price' => fake()->randomFloat(2, 1, 100),
            'sale_price' => fake()->randomFloat(2, 1, 150),
            'product_variant_id' => ProductVariant::factory(),
            'warehouse_id' => Warehouse::factory(),
        ];
    }
}
