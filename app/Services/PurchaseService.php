<?php

namespace App\Services;

use App\Http\Requests\PurchaseDetailRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Crea una compra y sus detalles, productos y variantes.
     *
     * @param array $data
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return Purchase
     * @throws \Throwable
     */
    public function createPurchase(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $purchase = $this->createBasePurchase($data, $user);
            $product = $this->getOrCreateProduct($data);

            $subtotal = 0;
            foreach ($data['details'] as $row) {
                $variant = $this->getOrCreateVariant($product, $row);
                $detail = $this->createPurchaseDetail($purchase, $variant, $row);
                $this->updateInventoryAndRegisterMovement($purchase, $variant, $row);
                $subtotal += ((int) $row['quantity']) * ((float) $row['unit_price']);
            }
            $this->updatePurchaseTotals($purchase, $subtotal);
            return $purchase;
        });
    }

    private function createBasePurchase(array $data, $user)
    {
        return Purchase::create([
            'reference' => $data['reference'] ?? null,
            'entity_id' => $data['entity_id'],
            'warehouse_id' => $data['warehouse_id'],
            'payment_method_id' => $data['payment_method_id'],
            'user_id' => $user->getAuthIdentifier(),
            'subtotal' => 0,
            'total' => 0,
        ]);
    }

    private function getOrCreateProduct(array $data)
    {
        $productId = $data['product']['id'] ?? null;
        if ($productId) {
            return Product::findOrFail($productId);
        }
        $productPayload = [
            'name' => $data['product']['name'],
            'description' => $data['product']['description'] ?? null,
            'barcode' => $data['product']['barcode'] ?? null,
            'code' => $data['product']['code'] ?? null,
            'sku' => $data['product']['sku'] ?? null,
            'status' => $data['product']['status'] ?? 'available',
            'brand_id' => $data['product']['brand_id'],
            'category_id' => $data['product']['category_id'],
            'tax_id' => $data['product']['tax_id'],
            'unit_measure_id' => $data['product']['unit_measure_id'],
            'entity_id' => $data['product']['entity_id'] ?? $data['entity_id'],
        ];
        return Product::create($productPayload);
    }

    private function getOrCreateVariant(Product $product, array $row)
    {
        $variant = ProductVariant::firstOrCreate(
            [
                'product_id' => $product->id,
                'color_id' => $row['color_id'],
                'size_id' => $row['size_id'],
            ],
            [
                'sku' => $row['sku'] ?? null,
                'code' => $row['code'] ?? null,
            ]
        );
        $variant->fill([
            'sku' => $row['sku'] ?? $variant->sku,
            'code' => $row['code'] ?? $variant->code,
        ]);
        $variant->save();
        return $variant;
    }

    private function createPurchaseDetail(Purchase $purchase, ProductVariant $variant, array $row)
    {
        return PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_variant_id' => $variant->id,
            'quantity' => (int) $row['quantity'],
            'unit_price' => (float) $row['unit_price'],
        ]);
    }

    private function updateInventoryAndRegisterMovement(Purchase $purchase, ProductVariant $variant, array $row)
    {
        $variantId = $variant->id;
        $warehouseId = $purchase->warehouse_id;
        $quantity = (int) $row['quantity'];
        $unitPrice = (float) $row['unit_price'];
        $salePrice = (float) $row['sale_price'];

        $inventory = Inventory::where('product_variant_id', $variantId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        if ($inventory) {
            $inventory->stock += $quantity;
            $inventory->purchase_price = $unitPrice;
            $inventory->sale_price = $salePrice;
            if (array_key_exists('min_stock', $row) && $row['min_stock'] !== null && $row['min_stock'] !== '') {
                $inventory->min_stock = (int) $row['min_stock'];
            }
            $inventory->save();
        } else {
            $inventory = Inventory::create([
                'product_variant_id' => $variantId,
                'warehouse_id' => $warehouseId,
                'stock' => $quantity,
                'purchase_price' => $unitPrice,
                'sale_price' => $salePrice,
                'min_stock' => (int) ($row['min_stock'] ?? 0),
            ]);
        }
        InventoryMovement::create([
            'inventory_id' => $inventory->id,
            'type' => 'in',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $unitPrice * $quantity,
            'reference' => 'Compra #' . $purchase->id,
            'notes' => 'Ingreso por compra',
            'user_id' => $purchase->user_id,
        ]);
    }

    private function updatePurchaseTotals(Purchase $purchase, $subtotal)
    {
        $purchase->subtotal = $subtotal;
        $purchase->total = $subtotal;
        $purchase->save();
    }
    /**
     * Actualiza una compra y sus detalles, productos y variantes.
     * Revierte inventario y movimientos previos, y aplica los nuevos datos.
     *
     * @param Purchase $purchase
     * @param array $data
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return Purchase
     * @throws \Throwable
     */
    public function updatePurchase(Purchase $purchase, array $data, $user)
    {
        return DB::transaction(function () use ($purchase, $data, $user) {
            // 1. Revertir inventario y eliminar movimientos previos
            foreach ($purchase->details as $detail) {
                $variant = $detail->productVariant;
                $inventory = Inventory::where('product_variant_id', $variant->id)
                    ->where('warehouse_id', $purchase->warehouse_id)
                    ->first();
                if ($inventory) {
                    $inventory->stock -= $detail->quantity;
                    $inventory->save();
                }
                // Eliminar movimientos de inventario relacionados a este detalle y compra
                InventoryMovement::where('inventory_id', $inventory ? $inventory->id : null)
                    ->where('type', 'in')
                    ->where('reference', 'Compra #' . $purchase->id)
                    ->delete();
            }
            // 2. Eliminar detalles previos
            $purchase->details()->delete();

            // 3. Actualizar datos base de la compra
            $purchase->reference = $data['reference'] ?? $purchase->reference;
            $purchase->entity_id = $data['entity_id'];
            $purchase->warehouse_id = $data['warehouse_id'];
            $purchase->payment_method_id = $data['payment_method_id'];
            $purchase->user_id = $user->getAuthIdentifier();
            $purchase->subtotal = 0;
            $purchase->total = 0;
            $purchase->save();

            // 4. Obtener o crear producto principal
            $product = $this->getOrCreateProduct($data);

            // 5. Crear nuevos detalles y actualizar inventario
            $subtotal = 0;
            foreach ($data['details'] as $row) {
                $variant = $this->getOrCreateVariant($product, $row);
                $detail = $this->createPurchaseDetail($purchase, $variant, $row);
                $this->updateInventoryAndRegisterMovement($purchase, $variant, $row);
                $subtotal += ((int) $row['quantity']) * ((float) $row['unit_price']);
            }
            $this->updatePurchaseTotals($purchase, $subtotal);
            return $purchase->fresh(['details.productVariant']);
        });
    }
}
