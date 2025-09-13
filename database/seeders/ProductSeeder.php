<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Camiseta Básica',
                'description' => 'Camiseta de algodón para uso diario.',
                'barcode' => '10000001',
                'image' => null,
                'status' => 'available',
                'brand_id' => 2, // Nike
                'tax_id' => 1, // IVA
                'unit_measure_id' => 1, // Unidad
                'entity_id' => 2, // Cliente ejemplo
            ],
            [
                'name' => 'Smartphone Samsung',
                'description' => 'Teléfono inteligente Samsung modelo A10.',
                'barcode' => '10000002',
                'image' => null,
                'status' => 'available',
                'brand_id' => 4, // Samsung
                'tax_id' => 1, // IVA
                'unit_measure_id' => 1, // Unidad
                'entity_id' => 1, // Proveedor ejemplo
            ],
            [
                'name' => 'Jabón de Tocador',
                'description' => 'Jabón para higiene personal.',
                'barcode' => '10000003',
                'image' => null,
                'status' => 'available',
                'brand_id' => 8, // Colgate
                'tax_id' => 2, // Exento
                'unit_measure_id' => 1, // Unidad
                'entity_id' => 3, // Mixto ejemplo
            ],
        ];

        foreach ($products as $product) {
            $productModel = Product::firstOrCreate($product);

            // Crear variante simple para el producto
            $variant = ProductVariant::firstOrCreate([
                'product_id' => $productModel->id,
                'color_id' => null,
                'size_id' => null,
            ], [
                'sku' => 'SKU-' . $productModel->id,
                'code' => $productModel->barcode,
            ]);

            // Crear inventario para la variante
            $inventory = Inventory::firstOrCreate([
                'product_variant_id' => $variant->id,
            ], [
                'stock' => 50,
                'min_stock' => 5,
                'purchase_price' => 100.00,
                'sale_price' => 150.00,
                'warehouse_id' => 1,
            ]);

            // Crear movimiento de entrada (compra)
            InventoryMovement::create([
                'type' => 'in',
                'quantity' => 50,
                'unit_price' => 100.00,
                'total_price' => 5000.00,
                'reference' => 'Compra inicial',
                'notes' => 'Ingreso de stock inicial',
                'user_id' => 1,
                'inventory_id' => $inventory->id,
            ]);

            // Crear movimiento de salida (venta)
            InventoryMovement::create([
                'type' => 'out',
                'quantity' => 5,
                'unit_price' => 150.00,
                'total_price' => 750.00,
                'reference' => 'Venta de prueba',
                'notes' => 'Salida de stock por venta de prueba',
                'user_id' => 1,
                'inventory_id' => $inventory->id,
            ]);
        }
    }
}
