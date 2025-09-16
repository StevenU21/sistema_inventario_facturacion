<?php

namespace Database\Factories;

use App\Models\AccountReceivable;
use App\Models\Entity;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Will be calculated after creating details
            'total' => 0,
            'is_credit' => fake()->boolean(),
            'tax_percentage' => null,
            'tax_amount' => null,
            'user_id' => User::inRandomOrder()->first()->id,
            'entity_id' => Entity::where('is_client', true)->inRandomOrder()->first()->id,
            'payment_method_id' => PaymentMethod::inRandomOrder()->first()->id,
            // Asignar sale_date entre el inicio del año y hoy
            'sale_date' => fake()->dateTimeBetween(date('Y-01-01'), 'now')->format('Y-m-d'),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($sale) {
            // Create between 1 and 5 sale details
            $detailsCount = fake()->numberBetween(1, 5);
            $subtotal = 0.0;

            for ($i = 0; $i < $detailsCount; $i++) {
                // Pick a random product variant
                $variant = ProductVariant::inRandomOrder()->first();
                if (!$variant) {
                    continue;
                }

                // Prefer inventory sale price if exists
                $inv = Inventory::where('product_variant_id', $variant->id)->inRandomOrder()->first();
                $unitPrice = $inv?->sale_price ?? fake()->randomFloat(2, 10, 500);
                $quantity = fake()->numberBetween(1, 5);
                $lineSubtotal = round($unitPrice * $quantity, 2);

                $discount = fake()->boolean(20) ? fake()->numberBetween(5, 20) : null; // percentage
                $discountAmount = $discount ? round($lineSubtotal * ($discount / 100), 2) : null;
                $subTotalAfterDiscount = $discountAmount ? round($lineSubtotal - $discountAmount, 2) : $lineSubtotal;

                SaleDetail::factory()->create([
                    'sale_id' => $sale->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotalAfterDiscount,
                    'discount' => $discount,
                    'discount_amount' => $discountAmount,
                ]);

                $subtotal += $subTotalAfterDiscount;
            }

            // Calculate taxes and total
            $applyTax = fake()->boolean(60);
            $taxPercentage = $applyTax ? fake()->randomElement([0, 5, 12, 15]) : null; // adapt to your tax table if needed
            $taxAmount = $taxPercentage ? round($subtotal * ($taxPercentage / 100), 2) : null;
            $total = round($subtotal + ($taxAmount ?? 0), 2);

            $sale->tax_percentage = $taxPercentage;
            $sale->tax_amount = $taxAmount;
            $sale->total = $total;
            $sale->save();

            // Handle accounts receivable if credit sale
            if ($sale->is_credit) {
                $ar = AccountReceivable::create([
                    'amount_due' => $sale->total,
                    'amount_paid' => 0,
                    'status' => 'pending',
                    'entity_id' => $sale->entity_id,
                    'sale_id' => $sale->id,
                ]);

                // Optionally create partial or full payments
                if (fake()->boolean(50)) {
                    $remaining = $ar->amount_due;
                    $iterations = fake()->numberBetween(1, 3);
                    for ($j = 0; $j < $iterations && $remaining > 0; $j++) {
                        $isLast = ($j === $iterations - 1);
                        $maxPayment = $isLast ? $remaining : max(0.01, round($remaining * fake()->randomFloat(2, 0.2, 0.7), 2));
                        $amount = $isLast ? $remaining : round(fake()->randomFloat(2, 0.01, $maxPayment), 2);

                        Payment::factory()->create([
                            'amount' => $amount,
                            'account_receivable_id' => $ar->id,
                            'payment_method_id' => $sale->payment_method_id,
                            'entity_id' => $sale->entity_id,
                            'user_id' => $sale->user_id,
                        ]);

                        $remaining = round($remaining - $amount, 2);
                    }

                    $paid = round($ar->payments()->sum('amount'), 2);
                    $ar->amount_paid = $paid;
                    $ar->status = $paid >= $ar->amount_due ? 'paid' : 'pending';
                    $ar->save();
                }
            }
        });
    }
}
