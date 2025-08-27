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
        'brands' => ['read'],
        'categories' => [],
        'backups' => ['read'],
        'companies' => ['read', 'create', 'update'],
        'unit_measures' => [],
        'departments' => [],
        'municipalities' => [],
        'payment_methods' => [],
        'taxes' => [],
    ];

    const SPECIAL_PERMISSIONS = [
        'permissions' => ['assign permissions', 'revoke permissions'],
        'users' => ['reactivate users'],
        'audits' => ['export audits'],
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
        );

        $cashierRole->givePermissionTo($cashierPermissions);
    }
}
