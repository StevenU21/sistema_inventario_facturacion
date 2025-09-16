<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Entity;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\PaymentMethod;

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
            'reference' => fake()->unique()->bothify('PUR-########'),
            'subtotal' => 0,
            'total' => 0,
            'entity_id' => Entity::where('is_supplier', true)->inRandomOrder()->first()?->id,
            'warehouse_id' => Warehouse::inRandomOrder()->first()?->id,
            'user_id' => User::inRandomOrder()->first()?->id,
            'payment_method_id' => PaymentMethod::inRandomOrder()->first()?->id,
        ];
    }
}
