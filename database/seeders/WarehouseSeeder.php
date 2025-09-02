<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Almacén Principal',
                'address' => 'Avenida Central, Condega',
                'description' => 'Almacén principal de la tienda',
            ],
            [
                'name' => 'Almacén Secundario',
                'address' => 'Zona Industrial, Estelí',
                'description' => 'Almacén secundario para productos de alto volumen',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::firstOrCreate($warehouse);
        }
    }
}
