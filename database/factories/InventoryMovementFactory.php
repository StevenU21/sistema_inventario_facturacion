<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryMovement>
 */
class InventoryMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    $userId = User::inRandomOrder()->value('id');
        return [
            // default to an 'in' movement
            'type' => 'in',
            'adjustment_reason' => null,
            'quantity' => fake()->numberBetween(1, 50),
            'unit_price' => fake()->randomFloat(2, 1, 100),
            'total_price' => fn (array $attrs) => ($attrs['quantity'] ?? 0) * ($attrs['unit_price'] ?? 0),
            'reference' => fake()->optional()->uuid(),
            'notes' => fake()->optional()->paragraph(),
            'user_id' => $userId,
            'inventory_id' => null,
        ];
    }

    public function entry(): static
    {
        return $this->state(fn () => [
            'type' => 'in',
            'adjustment_reason' => null,
        ]);
    }

    public function exit(): static
    {
        return $this->state(fn () => [
            'type' => 'out',
            'adjustment_reason' => null,
        ]);
    }

    public function adjustment(string $reason = 'correction'): static
    {
        // Map reasons to enum values; correction|physical_count -> in, damage|theft -> out
        $reason = strtolower($reason);
        $map = [
            'correccion' => 'correction',
            'conteo' => 'physical_count',
            'conteo fisico' => 'physical_count',
            'correction' => 'correction',
            'physical_count' => 'physical_count',
            'damage' => 'damage',
            'danio' => 'damage',
            'daño' => 'damage',
            'theft' => 'theft',
            'robo' => 'theft',
            'purchase_price' => 'purchase_price',
            'sale_price' => 'sale_price',
        ];
        $enumReason = $map[$reason] ?? 'correction';
        $type = in_array($enumReason, ['correction', 'physical_count', 'purchase_price', 'sale_price']) ? 'adjustment' : 'adjustment';
        return $this->state(fn () => [
            'type' => 'adjustment',
            'adjustment_reason' => $enumReason,
        ]);
    }
}
