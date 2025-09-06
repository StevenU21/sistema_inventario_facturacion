<?php

namespace Database\Seeders;

use App\Models\Company;
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
            'ruc' => null,
            'logo' => null,
            'description' => 'Tienda de ropa y accesorios en Condega, Estelí.',
            'address' => 'Del juzgado de Condega 2 cuadras al norte.',
            'phone' => '58574776',
            'email' => 'blessageneral@gmail.com',
        ]);
    }
}
