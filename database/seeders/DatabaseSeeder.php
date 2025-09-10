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
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Warehouse;

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

        $this->call(DepartmentSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(UnitMeasureSeeder::class);
        $this->call(WarehouseSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(SizeSeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(EntitySeeder::class);
        $this->call(ProductSeeder::class);
        // Warehouse::factory()->count(3)->create();

        // Product::factory()->count(400)->create()->each(function ($product) {

        //     ProductVariant::factory()->simple()->create([
        //         'product_id' => $product->id,
        //     ]);

        //     $variantsCount = rand(0, 4);
        //     for ($i = 0; $i < $variantsCount; $i++) {
        //         ProductVariant::factory()->withColorSize()->create([
        //             'product_id' => $product->id,
        //         ]);
        //     }
        // });

        // Purchase::factory()->count(3000)->create()->each(function ($purchase) {
        //     $detailsCount = rand(1, 5);
        //     $subtotal = 0;
        //     for ($i = 0; $i < $detailsCount; $i++) {
        //         $variant = ProductVariant::inRandomOrder()->first();
        //         $quantity = rand(1, 10);

        //         $inv = Inventory::where('product_variant_id', $variant->id)
        //             ->where('warehouse_id', $purchase->warehouse_id)
        //             ->first();
        //         $unitPrice = $inv?->purchase_price ?? rand(10, 100);
        //         $lineTotal = $quantity * $unitPrice;
        //         $subtotal += $lineTotal;
        //         PurchaseDetail::factory()->create([
        //             'purchase_id' => $purchase->id,
        //             'product_variant_id' => $variant->id,
        //             'quantity' => $quantity,
        //             'unit_price' => $unitPrice,
        //         ]);

        //         $inventory = Inventory::firstOrCreate(
        //             [
        //                 'product_variant_id' => $variant->id,
        //                 'warehouse_id' => $purchase->warehouse_id,
        //             ],
        //             [
        //                 'stock' => 0,
        //                 'min_stock' => rand(0, 10),
        //                 'purchase_price' => $unitPrice,
        //                 'sale_price' => round($unitPrice * 1.3, 2),
        //             ]
        //         );

        //         $inventory->stock += $quantity;
        //         $inventory->purchase_price = $unitPrice; 
        //         $inventory->save();
        //         InventoryMovement::create([
        //             'type' => 'in',
        //             'adjustment_reason' => null,
        //             'quantity' => $quantity,
        //             'unit_price' => $unitPrice,
        //             'total_price' => $lineTotal,
        //             'reference' => $purchase->reference,
        //             'notes' => 'Entrada por compra',
        //             'user_id' => $purchase->user_id ?? User::query()->value('id'),
        //             'inventory_id' => $inventory->id,
        //         ]);
        //     }
        //     $purchase->subtotal = $subtotal;
        //     $purchase->total = $subtotal;
        //     $purchase->save();
        // });
    }
}
