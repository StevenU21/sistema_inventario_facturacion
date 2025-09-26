<?php

namespace Database\Seeders;

use App\Classes\PermissionManager;
use Illuminate\Database\Seeder;

class RolesAndPermissionSeeder extends Seeder
{
    const PERMISSIONS = [
        'users' => [],
        'permissions' => ['read'],
        'audits' => ['read'],
        'brands' => [],
        'categories' => [],
        'backups' => ['read'],
        'companies' => ['read', 'create', 'update'],
        'unit_measures' => [],
        'departments' => [],
        'municipalities' => [],
        'payment_methods' => [],
        'taxes' => [],
        'entities' => ['destroy'],
        'products' => [],
        'product_variants' => [],
        'roles' => [],
        'warehouses' => [],
        'inventories' => [],
        'inventory_movements' => ['read'],
        'sizes' => [],
        'colors' => [],
        'purchases' => [],
        'kardex' => ['read', 'create'],
        'sales' => ['read', 'create', 'update'],
        'account_receivables' => ['read', 'create', 'update'],
        'payments' => ['read', 'create', 'update'],
        'quotations' => ['read', 'create', 'update'],
        'dashboard' => ['read'],
    ];

    const SPECIAL_PERMISSIONS = [
        'permissions' => ['assign permissions', 'revoke permissions'],
        'products' => ['export products'],
        'product_variants' => ['export product_variants'],
        'users' => ['export users'],
        'audits' => ['export audits'],
        'entities' => ['read suppliers', 'create suppliers', 'update suppliers', 'read clients', 'create clients', 'update clients', 'export entities'],
        'inventory_movements' => ['export inventory_movements'],
        'inventories' => ['export inventories'],
        'kardex' => ['export kardex'],
        'purchases' => ['export purchases'],
        'sales' => ['export sales', 'generate invoice'],
        'account_receivables' => ['export account_receivables'],
        'payments' => ['export payments'],
        'quotations' => ['export quotations'],
        'warehouses' => ['export warehouses'],
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $rolesDefinition = [
            'admin' => '*',
            'cashier' => [
                'companies' => 'read',
                'entities' => [
                    'read clients',
                    'create clients',
                    'update clients',
                    'export entities'
                ],
                'products' => 'read',
                'sales' => ['read', 'create', 'export sales'],
                'quotations' => ['read', 'create', 'update', 'export quotations'],
                'account_receivables' => ['read', 'create', 'export account_receivables'],
                'payments' => ['read', 'create'],
                'dashboard' => 'read',
            ],
        ];

        PermissionManager::make(self::PERMISSIONS, self::SPECIAL_PERMISSIONS)
            ->withRoles($rolesDefinition)
            ->sync();
    }
}
