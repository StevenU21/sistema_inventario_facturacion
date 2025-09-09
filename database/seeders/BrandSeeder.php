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
            'Puma',
            'Gucci',
            'Prada',
            'Chanel',
            'Louis Vuitton',
            'Zara',
            'H&M',
            'Mango',
            'Forever 21',
            'Calvin Klein',
            'Ralph Lauren',
            'Tommy Hilfiger',
            'Levi\'s',
            'Gap',
            'Coach',
            'Kate Spade'
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand]);
        }
    }
}
