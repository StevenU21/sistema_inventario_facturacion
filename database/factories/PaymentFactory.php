<?php

namespace Database\Factories;

use App\Models\AccountReceivable;
use App\Models\Entity;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 1, 500),
            'account_receivable_id' => AccountReceivable::inRandomOrder()->value('id'),
            'payment_method_id' => PaymentMethod::inRandomOrder()->value('id'),
            'user_id' => User::inRandomOrder()->value('id'),
            'entity_id' => Entity::inRandomOrder()->value('id'),
            // Asignar payment_date entre el inicio del año y hoy
            'payment_date' => fake()->dateTimeBetween(date('Y-01-01'), 'now')->format('Y-m-d'),
        ];
    }
}
