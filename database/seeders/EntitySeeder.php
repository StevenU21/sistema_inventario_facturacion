<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = [
            [
                'first_name' => 'Limber',
                'last_name' => 'Rivas',
                'identity_card' => '001-120190-0001U',
                'ruc' => '0011201902001U',
                'email' => 'proveedor.nica@ejemplo.com',
                'phone' => '+505 85857001',
                'address' => 'Zona Industrial, Estelí',
                'description' => 'Proveedor nicaragüense',
                'is_client' => false,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
            [
                'first_name' => 'Raul',
                'last_name' => 'Valenzuela',
                'identity_card' => '001-120290-0002U',
                'ruc' => '0011202904002U',
                'email' => 'cliente.nica@ejemplo.com',
                'phone' => '+505 85845002',
                'address' => 'Barrio Central, Condega',
                'description' => 'Cliente nicaragüense',
                'is_client' => true,
                'is_supplier' => false,
                'is_active' => true,
                'municipality_id' => 2,
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Fuentes',
                'identity_card' => '001-120390-0003U',
                'ruc' => '0011203903003U',
                'email' => 'mixto.nica@ejemplo.com',
                'phone' => '+505 85854003',
                'address' => 'Avenida Principal, Estelí',
                'description' => 'Proveedor y cliente nicaragüense',
                'is_client' => true,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
            [
                'first_name' => 'David Manuel',
                'last_name' => 'Rivas Chavarria',
                'identity_card' => '001-120490-0004U',
                'ruc' => '0011204908004U',
                'email' => 'david.nica@ejemplo.com',
                'phone' => '+505 85850004',
                'address' => 'Sin dirección',
                'description' => 'Entidad nicaragüense',
                'is_client' => false,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
            [
                'first_name' => 'Jefry',
                'last_name' => 'Cornejo',
                'identity_card' => '001-120490-0005U',
                'ruc' => '0011204909005U',
                'email' => 'jefry.nica@ejemplo.com',
                'phone' => '+505 85850604',
                'address' => 'Sin dirección',
                'description' => 'Entidad nicaragüense',
                'is_client' => false,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
            [
                'first_name' => 'Kevon           ',
                'last_name' => 'Mendez',
                'identity_card' => '001-120490-0006U',
                'ruc' => '0011204900106U',
                'email' => 'kevon.nica@ejemplo.com',
                'phone' => '+505 85852004',
                'address' => 'Sin dirección',
                'description' => 'Entidad nicaragüense',
                'is_client' => false,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
        ];

        foreach ($entities as $entity) {
            Entity::firstOrCreate($entity);
        }
    }
}
