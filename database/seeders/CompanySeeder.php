<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::firstOrCreate([
            'name' => 'BlessaBoutique',
            'ruc' => '1234567890001',
            'logo' => null,
            'description' => 'Tienda de ropa y accesorios en Condega, Estelí.',
            'address' => 'Avenida Central, Condega, Estelí',
            'phone' => '8888-9999',
            'email' => 'info@blessaboutique.com',
        ]);
    }
}
