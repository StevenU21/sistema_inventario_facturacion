<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Tax;
use App\Models\UnitMeasure;
use App\Models\Entity;
use App\Models\ProductStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'image' => null,
            'description' => fake()->sentence(10),
            // App uses 'available' | 'discontinued'
            'status' => 'available',
            'brand_id' => Brand::inRandomOrder()->first()?->id,
            'tax_id' => Tax::inRandomOrder()->first()?->id,
            'unit_measure_id' => UnitMeasure::inRandomOrder()->first()?->id,
            'entity_id' => Entity::inRandomOrder()->first()?->id
        ];
    }

    /**
     * After creating a product, attach variants and inventories.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($product) {
            // Create one simple variant
            $simpleVariant = \App\Models\ProductVariant::factory()->simple()->create([
                'product_id' => $product->id,
            ]);

            // Maybe create a few colored/size variants
            $extra = fake()->numberBetween(0, 3);
            for ($i = 0; $i < $extra; $i++) {
                \App\Models\ProductVariant::factory()->withColorSize()->create([
                    'product_id' => $product->id,
                ]);
            }

            // Create inventories for 1-2 warehouses for the simple variant
            $warehouses = \App\Models\Warehouse::inRandomOrder()->take(fake()->numberBetween(1, 2))->get();
            foreach ($warehouses as $wh) {
                \App\Models\Inventory::factory()->create([
                    'product_variant_id' => $simpleVariant->id,
                    'warehouse_id' => $wh->id,
                ]);
            }
        });
    }
}
