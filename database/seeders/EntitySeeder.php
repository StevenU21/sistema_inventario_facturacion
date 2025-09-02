<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'last_name' => 'Martínez',
                'identity_card' => '001-120190-0001U',
                'ruc' => '0011201900001U',
                'email' => 'proveedor.nica@ejemplo.com',
                'phone' => '+505 85850001',
                'address' => 'Zona Industrial, Estelí',
                'description' => 'Proveedor nicaragüense',
                'is_client' => false,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
            [
                'first_name' => 'Candida',
                'last_name' => 'Cardoza',
                'identity_card' => '001-120290-0002U',
                'ruc' => '0011202900002U',
                'email' => 'cliente.nica@ejemplo.com',
                'phone' => '+505 85850002',
                'address' => 'Barrio Central, Condega',
                'description' => 'Cliente nicaragüense',
                'is_client' => true,
                'is_supplier' => false,
                'is_active' => true,
                'municipality_id' => 2,
            ],
            [
                'first_name' => 'James',
                'last_name' => 'López',
                'identity_card' => '001-120390-0003U',
                'ruc' => '0011203900003U',
                'email' => 'mixto.nica@ejemplo.com',
                'phone' => '+505 85850003',
                'address' => 'Avenida Principal, Estelí',
                'description' => 'Proveedor y cliente nicaragüense',
                'is_client' => true,
                'is_supplier' => true,
                'is_active' => true,
                'municipality_id' => 1,
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Ramírez',
                'identity_card' => '001-120490-0004U',
                'ruc' => '0011204900004U',
                'email' => 'inactivo.nica@ejemplo.com',
                'phone' => '+505 85850004',
                'address' => 'Sin dirección',
                'description' => 'Entidad nicaragüense inactiva',
                'is_client' => false,
                'is_supplier' => false,
                'is_active' => false,
                'municipality_id' => 1,
            ],
        ];

        foreach ($entities as $entity) {
            Entity::firstOrCreate($entity);
        }
    }
}
