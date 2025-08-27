<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Department;
use App\Models\Profile;
use App\Models\UnitMeasure;
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

        Category::factory()->count(26)->create();
        Brand::factory()->count(33)->create();
        Company::factory()->count(1)->create();
        UnitMeasure::factory()->count(12)->create();
        Department::factory()->count(45)->create();
    }
}
