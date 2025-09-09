<?php

namespace App\Classes;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryMovementManager
{
    public static function transfer(Inventory $inventory, Request $request)
    {
        $quantity = $request->filled('quantity') ? (int) $request->input('quantity') : $inventory->stock;
        $destWarehouseId = $request->input('destination_warehouse_id');

        \DB::beginTransaction();
        try {
            // Validaciones básicas
            if (!$destWarehouseId) {
                \DB::rollBack();
                return ['error' => ['error' => 'Debe seleccionar el almacén destino.']];
            }
            if ($destWarehouseId == $inventory->warehouse_id) {
                \DB::rollBack();
                return ['error' => ['error' => 'El almacén destino debe ser diferente al de origen.']];
            }
            if ($quantity <= 0 || $quantity > $inventory->stock) {
                \DB::rollBack();
                return ['error' => ['error' => 'Cantidad inválida para transferir.']];
            }
            // Buscar inventario destino
            $destInventory = Inventory::where('product_variant_id', $inventory->product_variant_id)
                ->where('warehouse_id', $destWarehouseId)
                ->first();

            // Actualizar inventario origen
            $inventory->stock -= $quantity;
            $inventory->save();
            $transferMovement = $inventory->inventoryMovements()->create([
                'type' => 'transfer',
                'quantity' => $quantity,
                'unit_price' => $inventory->purchase_price,
                'total_price' => $inventory->purchase_price * $quantity,
                // Comentario de referencia
                'reference' => 'Transferencia a almacén destino',
                // Comentario de notas
                'notes' => 'Movimiento generado por transferencia',
                'user_id' => auth()->id(),
            ]);

            // Actualizar o crear inventario destino
        if (!$destInventory) {
                $destInventory = Inventory::create([
            'product_variant_id' => $inventory->product_variant_id,
                    'warehouse_id' => $destWarehouseId,
                    'stock' => $quantity,
                    'min_stock' => $inventory->min_stock,
                    'purchase_price' => $inventory->purchase_price,
                    'sale_price' => $inventory->sale_price,
                ]);
            } else {
                $destInventory->stock += $quantity;
                $destInventory->save();
            }

            $destMovement = $destInventory->inventoryMovements()->create([
                'type' => 'in',
                'quantity' => $quantity,
                'unit_price' => $inventory->purchase_price,
                'total_price' => $inventory->purchase_price * $quantity,
                // Comentario de referencia
                'reference' => 'Transferido desde almacén origen',
                // Comentario de notas
                'notes' => 'Movimiento generado por transferencia',
                'user_id' => auth()->id(),
            ]);

            \DB::commit();
            return $transferMovement;
        } catch (\Exception $e) {
            \DB::rollBack();
            return ['error' => ['error' => 'Error en la transferencia: ' . $e->getMessage()]];
        }
    }

    public static function adjust(Inventory $inventory, Request $request)
    {
        $reason = $request->input('adjustment_reason');
        $quantity = $request->filled('quantity') ? (int) $request->input('quantity') : null;

        \DB::beginTransaction();
        try {
            // Validación extra: no guardar si el precio de compra es mayor que el de venta
            if ($reason === 'purchase_price' && $request->filled('purchase_price')) {
                $newPurchase = $request->input('purchase_price');
                $salePrice = $inventory->sale_price;
                // Si el usuario está ajustando ambos precios en el mismo request
                if ($request->filled('sale_price')) {
                    $salePrice = $request->input('sale_price');
                }
                if ($salePrice !== null && $newPurchase > $salePrice) {
                    \DB::rollBack();
                    return ['error' => ['error' => 'No se puede guardar: el precio de compra es mayor que el precio de venta.']];
                }
                $inventory->purchase_price = $newPurchase;
            }
            // Validación extra: no guardar si el precio de venta es menor que el de compra
            if ($reason === 'sale_price' && $request->filled('sale_price')) {
                $newSale = $request->input('sale_price');
                $purchasePrice = $inventory->purchase_price;
                // Si el usuario está ajustando ambos precios en el mismo request
                if ($request->filled('purchase_price')) {
                    $purchasePrice = $request->input('purchase_price');
                }
                if ($purchasePrice !== null && $newSale < $purchasePrice) {
                    \DB::rollBack();
                    return ['error' => ['error' => 'No se puede guardar: el precio de venta es menor que el precio de compra.']];
                }
                $inventory->sale_price = $newSale;
            }

            // Ajuste de stock
            if (in_array($reason, ['correction', 'physical_count', 'damage', 'theft']) && $quantity !== null) {
                $inventory->stock += in_array($reason, ['damage', 'theft']) ? -$quantity : $quantity;
            }
            if ($inventory->purchase_price === null)
                $inventory->purchase_price = 0;
            if ($inventory->sale_price === null)
                $inventory->sale_price = 0;
            $inventory->save();

            // Traducción de reason a español
            $reasonEs = [
                'correction' => 'corrección',
                'physical_count' => 'conteo físico',
                'damage' => 'daño',
                'theft' => 'robo',
                'purchase_price' => 'precio de compra',
                'sale_price' => 'precio de venta',
            ];
            $reasonLabel = $reasonEs[$reason] ?? $reason;

            // Generar referencia y notas automáticas según el tipo de ajuste
            $autoReference = "Ajuste manual ({$reasonLabel})";
            $autoNotes = '';
            if (in_array($reason, ['correction', 'physical_count', 'damage', 'theft']) && $quantity !== null) {
                $autoReference = "Ajuste de cantidad ({$reasonLabel})";
                $autoNotes = "Cantidad ajustada en inventario. Nuevo stock: {$inventory->stock}.";
            } else if ($reason === 'purchase_price' && $request->filled('purchase_price')) {
                $autoReference = "Ajuste de precio de compra ({$reasonLabel})";
                $autoNotes = "Precio de compra actualizado a {$inventory->purchase_price}.";
            } else if ($reason === 'sale_price' && $request->filled('sale_price')) {
                $autoReference = "Ajuste de precio de venta ({$reasonLabel})";
                $autoNotes = "Precio de venta actualizado a {$inventory->sale_price}.";
            }

            $movement = $inventory->inventoryMovements()->create([
                'type' => 'adjustment',
                'adjustment_reason' => $reason,
                'quantity' => $quantity ?? 0,
                'unit_price' => $inventory->purchase_price,
                'total_price' => $inventory->purchase_price * ($quantity ?? 0),
                'reference' => $request->input('reference') ?: $autoReference,
                'notes' => $request->input('notes') ?: $autoNotes,
                'user_id' => auth()->id(),
            ]);
            \DB::commit();
            return $movement;
        } catch (\Exception $e) {
            \DB::rollBack();
            return ['error' => ['error' => 'Error en el ajuste: ' . $e->getMessage()]];
        }
    }
}
