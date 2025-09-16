<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Solo productos de ropa y calzado
        $products = [
            [
                'name' => 'Camiseta Deportiva',
                'description' => 'Camiseta deportiva de microfibra.',
                'code' => '10010001',
                'image' => null,
                'status' => 'available',
                'brand_id' => 2, // Nike
                'tax_id' => 1, // IVA
                'unit_measure_id' => 1, // Unidad
                'entity_id' => 2, // Cliente ejemplo
            ],
            [
                'name' => 'Zapatillas Running',
                'description' => 'Zapatillas cómodas para correr.',
                'code' => '10020001',
                'image' => null,
                'status' => 'available',
                'brand_id' => 3, // Adidas
                'tax_id' => 1, // IVA
                'unit_measure_id' => 1, // Unidad
                'entity_id' => 2, // Cliente ejemplo
            ],
        ];

        // Obtener todos los colores y tallas
        $colorIds = Color::pluck('id')->toArray();
        $sizeIds = Size::pluck('id')->toArray();

        foreach ($products as $product) {
            $productModel = Product::firstOrCreate($product);

            // Crear variantes combinando colores y tallas
            foreach ($colorIds as $colorId) {
                foreach ($sizeIds as $sizeId) {
                    $variant = ProductVariant::firstOrCreate([
                        'product_id' => $productModel->id,
                        'color_id' => $colorId,
                        'size_id' => $sizeId,
                    ], [
                        'sku' => null,
                        'code' => null,
                    ]);

                    // Crear inventario para cada variante
                    $inventory = Inventory::firstOrCreate([
                        'product_variant_id' => $variant->id,
                    ], [
                        'stock' => 50,
                        'min_stock' => 5,
                        'purchase_price' => 100.00,
                        'sale_price' => 150.00,
                        'warehouse_id' => 1,
                    ]);

                    // Movimientos de inventario
                    InventoryMovement::create([
                        'type' => 'in',
                        'quantity' => 50,
                        'unit_price' => 100.00,
                        'total_price' => 50 * 100.00,
                        'reference' => 'Compra inicial',
                        'notes' => 'Ingreso de stock inicial',
                        'user_id' => 1,
                        'inventory_id' => $inventory->id,
                    ]);
                    InventoryMovement::create([
                        'type' => 'out',
                        'quantity' => 5,
                        'unit_price' => 150.00,
                        'total_price' => 5 * 150.00,
                        'reference' => 'Venta de prueba',
                        'notes' => 'Salida de stock por venta de prueba',
                        'user_id' => 1,
                        'inventory_id' => $inventory->id,
                    ]);
                }
            }

        }
    }
}
