<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Company;
use App\Models\Department;
use App\Models\Entity;
use App\Models\Municipality;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Profile;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Size;
use App\Models\Tax;
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

        Category::factory()->count(12)->create();
        Brand::factory()->count(13)->create();
        Company::factory()->count(1)->create();
        UnitMeasure::factory()->count(4)->create();
        Department::factory()->count(10)->create();
        Municipality::factory()->count(35)->create();
        PaymentMethod::factory()->count(3)->create();
        Tax::factory()->count(3)->create();
        Entity::factory()->count(20)->create();
        Size::factory()->count(8)->create();
        Color::factory()->count(6)->create();

        Product::factory()->count(100)->create()->each(function ($product) {
            $variantsCount = rand(1, 5);
            for ($i = 0; $i < $variantsCount; $i++) {
                ProductVariant::factory()->create([
                    'product_id' => $product->id
                ]);
            }
        });

        Purchase::factory()->count(20)->create()->each(function ($purchase) {
            $detailsCount = rand(1, 5);
            $subtotal = 0;
            for ($i = 0; $i < $detailsCount; $i++) {
                $variant = ProductVariant::inRandomOrder()->first();
                $quantity = rand(1, 10);
                $unitPrice = $variant->price ?? rand(10, 100);
                $lineTotal = $quantity * $unitPrice;
                $subtotal += $lineTotal;
                PurchaseDetail::factory()->create([
                    'purchase_id' => $purchase->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }
            $purchase->subtotal = $subtotal;
            $purchase->total = $subtotal;
            $purchase->save();
        });

        // $this->call(CategorySeeder::class);
        // $this->call(BrandSeeder::class);
        // $this->call(CompanySeeder::class);
        // $this->call(UnitMeasureSeeder::class);
        // $this->call(DepartmentSeeder::class);
        // $this->call(PaymentMethodSeeder::class);
        // $this->call(TaxSeeder::class);
        // $this->call(EntitySeeder::class);
        // $this->call(WarehouseSeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(SizeSeeder::class);
        // $this->call(ColorSeeder::class);
    }
}
