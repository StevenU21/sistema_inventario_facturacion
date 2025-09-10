<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => fake()->unique()->bothify('???-########'),
            'barcode' => fake()->unique()->ean13(),
            // Ensure it links to a product by default
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            // Allow nullable for simple variants; specialized states provided below
            'color_id' => null,
            'size_id' => null,
        ];
    }

    /**
     * State: simple variant without color/size.
     */
    public function simple(): static
    {
        return $this->state(fn () => [
            'color_id' => null,
            'size_id' => null,
        ]);
    }

    /**
     * State: with color and size.
     */
    public function withColorSize(): static
    {
        return $this->state(function () {
            return [
                'color_id' => Color::inRandomOrder()->first()?->id,
                'size_id' => Size::inRandomOrder()->first()?->id,
            ];
        });
    }
}
