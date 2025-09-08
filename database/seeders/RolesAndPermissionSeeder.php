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
    ];

    const SPECIAL_PERMISSIONS = [
        'permissions' => ['assign permissions', 'revoke permissions'],
        'products' => ['export products'],
        'product_variants' => ['export product_variants'],
        'users' => ['reactivate users', 'export users'],
        'audits' => ['export audits'],
        'entities' => ['read suppliers', 'create suppliers', 'update suppliers', 'read clients', 'create clients', 'update clients', 'export clients', 'export suppliers'],
        'inventory_movements' => ['export inventory_movements'],
        'inventories' => ['export inventories'],
        'kardex' => ['export kardex'],
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
            $this->filterPermissions('users')->only(['read users'])->get(),
            $this->filterPermissions('categories')->only(['read categories'])->get(),
            $this->filterPermissions('brands')->only(['read brands'])->get(),
            $this->filterPermissions('companies')->only(['read companies'])->get(),
            $this->filterPermissions('unit_measures')->only(['read unit_measures'])->get(),
            $this->filterPermissions('departments')->only(['read departments'])->get(),
            $this->filterPermissions('municipalities')->only(['read municipalities'])->get(),
            $this->filterPermissions('payment_methods')->only(['read payment_methods'])->get(),
            $this->filterPermissions('taxes')->only(['read taxes'])->get(),
            $this->filterPermissions('entities')->only(['read clients', 'create clients', 'update clients'])->get(),
            $this->filterPermissions('products')->only(['read products'])->get(),
            $this->filterPermissions('roles')->only(['read roles'])->get(),
            $this->filterPermissions('warehouses')->only(['read warehouses'])->get(),
            $this->filterPermissions('inventories')->only(['read inventories'])->get(),
            $this->filterPermissions('inventory_movements')->only(['read inventory_movements'])->get(),
            $this->filterPermissions('sizes')->only(['read sizes'])->get(),
            $this->filterPermissions('colors')->only(['read colors'])->get(),
            $this->filterPermissions('product_variants')->only(['read product_variants'])->get(),
        );

        $cashierRole->givePermissionTo($cashierPermissions);
    }
}
