<?php

namespace Database\Seeders;

use App\Models\UnitMeasure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Unidad',
            'Caja',
            'Paquete',
            'Docena',
            'Kilogramo',
            'Gramo',
            'Litro',
            'Mililitro',
            'Metro',
            'Centímetro',
            'Galón',
            'Saco',
            'Bolsa',
        ];

        foreach ($units as $unit) {
            UnitMeasure::firstOrCreate(['name' => $unit]);
        }
    }
}
