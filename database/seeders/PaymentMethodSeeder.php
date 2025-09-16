<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            'Efectivo',
            'Tarjeta de crédito',
            'Tarjeta de débito',
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method]);
        }
    }
}
