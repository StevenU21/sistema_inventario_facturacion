<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Alimentos',
            'Bebidas',
            'Limpieza',
            'Higiene Personal',
            'Electrónica',
            'Ropa',
            'Calzado',
            'Papelería',
            'Juguetes',
            'Ferretería',
            'Mascotas',
            'Salud',
            'Belleza',
            'Hogar',
            'Automotriz',
            'Deportes',
            'Herramientas',
            'Accesorios',
            'Otros',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}
