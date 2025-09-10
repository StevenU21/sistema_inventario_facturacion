<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\InventoryMovement;
use App\Models\User;

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
            'product_variant_id' => ProductVariant::factory()->simple(),
            'warehouse_id' => Warehouse::factory(),
        ];
    }

    /**
     * After creating an inventory record, also log an entry movement reflecting its initial stock.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($inventory) {
            if ($inventory->stock > 0) {
                $userId = User::inRandomOrder()->value('id');
                InventoryMovement::create([
                    'type' => 'in',
                    'adjustment_reason' => null,
                    'quantity' => $inventory->stock,
                    'unit_price' => $inventory->purchase_price,
                    'total_price' => $inventory->stock * $inventory->purchase_price,
                    'reference' => 'SEED-INIT',
                    'notes' => 'Creado por factory',
                    'user_id' => $userId,
                    'inventory_id' => $inventory->id,
                ]);
            }
        });
    }
}
