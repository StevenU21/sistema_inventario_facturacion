<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Genérica',
            'Nike',
            'Adidas',
            'Samsung',
            'LG',
            'Sony',
            'Apple',
            'Colgate',
            'Nestlé',
            'Pepsi',
            'Coca-Cola',
            'Puma',
            'Dell',
            'HP',
            'Lenovo',
            'Procter & Gamble',
            'Unilever',
            'Bimbo',
            'Frito Lay'
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand]);
        }
    }
}
