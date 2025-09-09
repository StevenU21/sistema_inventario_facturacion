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
            ['name' => 'Rojo', 'code' => '#FF0000'],
            ['name' => 'Azul', 'code' => '#0000FF'],
            ['name' => 'Verde', 'code' => '#00FF00'],
            ['name' => 'Negro', 'code' => '#000000'],
            ['name' => 'Blanco', 'code' => '#FFFFFF'],
        ];
        foreach ($colors as $color) {
            Color::firstOrCreate(['name' => $color['name']], $color);
        }
    }
}
