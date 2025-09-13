<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brandData = [
            ['name' => 'HP', 'category' => 'Electrónica'],
            ['name' => 'Dell', 'category' => 'Electrónica'],
            ['name' => 'Acer', 'category' => 'Electrónica'],
            ['name' => 'Nike', 'category' => 'Calzado'],
            ['name' => 'Gucci', 'category' => 'Ropa'],
            ['name' => 'Prada', 'category' => 'Ropa'],
            ['name' => 'Chanel', 'category' => 'Belleza'],
            ['name' => 'Louis Vuitton', 'category' => 'Accesorios'],
        ];

        foreach ($brandData as $data) {
            $category = Category::where('name', $data['category'])->first();
            Brand::firstOrCreate(
                ['name' => $data['name']],
                ['category_id' => $category ? $category->id : null]
            );
        }
    }
}
