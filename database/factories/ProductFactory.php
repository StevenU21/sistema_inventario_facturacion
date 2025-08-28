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
            'purchase_price' => fake()->randomFloat(2, 10, 500),
            'sale_price' => fake()->randomFloat(2, 20, 1000),
            'stock' => fake()->numberBetween(0, 200),
            'min_stock' => fake()->numberBetween(0, 20),
            'brand_id' => Brand::inRandomOrder()->first()?->id,
            'category_id' => Category::inRandomOrder()->first()?->id,
            'tax_id' => Tax::inRandomOrder()->first()?->id,
            'unit_measure_id' => UnitMeasure::inRandomOrder()->first()?->id,
            'entity_id' => Entity::inRandomOrder()->first()?->id,
            'product_status_id' => ProductStatus::inRandomOrder()->first()?->id,
        ];
    }
}
