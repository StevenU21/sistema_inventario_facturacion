<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            ['name' => 'Rojo', 'hex_code' => '#FF0000'],
            ['name' => 'Azul', 'hex_code' => '#0000FF'],
            ['name' => 'Verde', 'hex_code' => '#00FF00'],
            ['name' => 'Negro', 'hex_code' => '#000000'],
            ['name' => 'Blanco', 'hex_code' => '#FFFFFF'],
        ];
        foreach ($colors as $color) {
            Color::firstOrCreate(['name' => $color['name']], $color);
        }
    }
}
