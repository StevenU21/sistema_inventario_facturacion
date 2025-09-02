<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'Tarjeta de Crédito',
            'Tarjeta de Débito',
            'Transferencia Bancaria',
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method]);
        }
    }
}
