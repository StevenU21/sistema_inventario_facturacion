<?php

namespace Database\Seeders;

use App\Classes\PermissionManager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        'kardex' => ['read'],
        'sales' => ['read', 'create', 'update'],
        'account_receivables' => ['read', 'create', 'update'],
        'payments' => ['read', 'create', 'update'],
        'quotations' => ['read', 'create', 'update'],
    ];

    const SPECIAL_PERMISSIONS = [
        'permissions' => ['assign permissions', 'revoke permissions'],
        'products' => ['export products'],
        'product_variants' => ['export product_variants'],
        'users' => ['reactivate users', 'export users'],
        'audits' => ['export audits'],
        'entities' => ['read suppliers', 'create suppliers', 'update suppliers', 'read clients', 'create clients', 'update clients', 'export entities'],
        'inventory_movements' => ['export inventory_movements'],
        'inventories' => ['export inventories'],
        'kardex' => ['export kardex', 'generate kardex'],
        'purchases' => ['export purchases'],
        'sales' => ['export sales', 'generate invoice'],
        'account_receivables' => ['export account_receivables'],
        'payments' => ['export payments'],
        'quotations' => ['export quotations'],
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $manager = new PermissionManager(self::PERMISSIONS, self::SPECIAL_PERMISSIONS);
        $allPermissions = $manager->get();

        $this->createPermissions($allPermissions);

        $this->assignPermissionsToRoles();
    }

    protected function createPermissions($permissions): void
    {
        foreach ($permissions as $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }
        }
    }

    protected function filterPermissions($permission): PermissionManager
    {
        $permissions = self::PERMISSIONS[$permission] ?? [];
        $specialPermissions = self::SPECIAL_PERMISSIONS[$permission] ?? [];

        return new PermissionManager([$permission => $permissions], [$permission => $specialPermissions]);
    }

    protected function assignPermissionsToRoles(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

        $adminRole->givePermissionTo(Permission::all());

        $cashierPermissions = array_merge(
            $this->filterPermissions('companies')->only(['read companies'])->get(),
            $this->filterPermissions('entities')->only(['read clients', 'create clients', 'update clients', 'export entities'])->get(),
            $this->filterPermissions('products')->only(['read products'])->get(),
            $this->filterPermissions('sales')->only(['read sales', 'create sales'])->get(),
            $this->filterPermissions('quotations')->only(['read quotations', 'create quotations', 'update quotations'])->get(),
            $this->filterPermissions('account_receivables')->only(['read account_receivables', 'create account_receivables'])->get(),
            $this->filterPermissions('payments')->only(['read payments', 'create payments'])->get()
        );

        $cashierRole->givePermissionTo($cashierPermissions);
    }
}
