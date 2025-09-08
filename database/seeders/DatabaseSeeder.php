<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionSeeder::class);

        $adminUser = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);
        Profile::factory()->create([
            'user_id' => $adminUser->id
        ]);
        $adminUser->assignRole('admin');

        $cashierUser = User::factory()->create([
            'first_name' => 'Cashier',
            'last_name' => 'User',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password')
        ]);
        Profile::factory()->create([
            'user_id' => $cashierUser->id
        ]);
        $cashierUser->assignRole('cashier');

        // Category::factory()->count(26)->create();
        // Brand::factory()->count(33)->create();
        // Company::factory()->count(1)->create();
        // UnitMeasure::factory()->count(12)->create();
        // Department::factory()->count(15)->create();
        // Municipality::factory()->count(50)->create();
        // PaymentMethod::factory()->count(10)->create();
        // Tax::factory()->count(10)->create();
        // Entity::factory()->count(50)->create();
        // Size::factory()->count(10)->create();
        // Color::factory()->count(10)->create();

        $this->call(CategorySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(UnitMeasureSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(EntitySeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(ProductSeeder::class);
    }
}
