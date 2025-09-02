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
            // Buscar inventario destino
            $destInventory = Inventory::where('product_id', $inventory->product_id)
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
                    'product_id' => $inventory->product_id,
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
        // ...existing code...
        $reason = $request->input('adjustment_reason');
        $quantity = $request->filled('quantity') ? (int) $request->input('quantity') : null;
        $notes = $request->input('notes');
        $reference = $request->input('reference');
        $unit_price = $request->filled('purchase_price') && $request->input('purchase_price') !== null ? $request->input('purchase_price') : $inventory->purchase_price;
        $sale_price = $request->filled('sale_price') && $request->input('sale_price') !== null ? $request->input('sale_price') : $inventory->sale_price;
        $oldStock = $inventory->stock;
        $oldPurchase = $inventory->purchase_price;
        $oldSale = $inventory->sale_price;

        // ...existing code...

        $stockChanged = false;
        $priceChanged = false;

        switch ($reason) {
            case 'correction':
            case 'physical_count':
            case 'damage':
            case 'theft':
                // Solo actualizar stock
                if ($quantity !== null) {
                    $subtractReasons = ['damage', 'theft'];
                    if (in_array($reason, $subtractReasons)) {
                        // ...existing code...
                        $inventory->stock -= $quantity;
                    } else {
                        // ...existing code...
                        $inventory->stock += $quantity;
                    }
                    $stockChanged = true;
                }
                break;
            case 'purchase_price':
                // Solo actualizar precio de compra
                if ($request->filled('purchase_price') && $unit_price != $oldPurchase) {
                    // ...existing code...
                    $inventory->purchase_price = $unit_price;
                    $priceChanged = true;
                }
                break;
            case 'sale_price':
                // Solo actualizar precio de venta
                if ($request->filled('sale_price') && $sale_price != $oldSale) {
                    // ...existing code...
                    $inventory->sale_price = $sale_price;
                    $priceChanged = true;
                }
                break;
            default:
                // ...existing code...
                // No hacer nada si la razón no es válida
                break;
        }

        // Asegurar que nunca sean null antes de guardar
        if ($inventory->purchase_price === null) {
            $inventory->purchase_price = 0;
        }
        if ($inventory->sale_price === null) {
            $inventory->sale_price = 0;
        }
        // ...existing code...
        $inventory->save();

        // Generar notes y reference automáticamente
        $autoReference = 'Ajuste manual';
        $autoNotes = '';
        if ($stockChanged && $priceChanged) {
            $autoReference = 'Ajuste de cantidad y precios';
            $autoNotes = "Cantidad ajustada de $oldStock a {$inventory->stock}. Precio compra de $oldPurchase a $unit_price. Precio venta de $oldSale a $sale_price.";
        } elseif ($stockChanged) {
            $autoReference = 'Ajuste de cantidad';
            $autoNotes = "Cantidad ajustada de $oldStock a {$inventory->stock}.";
        } elseif ($priceChanged) {
            if ($reason === 'purchase_price') {
                $autoReference = 'Ajuste de precio de compra';
                $autoNotes = "Precio compra de $oldPurchase a $unit_price.";
            } else if ($reason === 'sale_price') {
                $autoReference = 'Ajuste de precio de venta';
                $autoNotes = "Precio venta de $oldSale a $sale_price.";
            } else {
                $autoReference = 'Ajuste de precios';
                $autoNotes = "Precio compra de $oldPurchase a $unit_price. Precio venta de $oldSale a $sale_price.";
            }
        }
        // Si el usuario envía notes/reference, se usan, si no, se generan
        $finalReference = $reference ?: $autoReference;
        $finalNotes = $notes ?: $autoNotes;

        $movement = $inventory->inventoryMovements()->create([
            'type' => 'adjustment',
            'adjustment_reason' => $reason,
            'quantity' => $quantity ?? 0,
            'unit_price' => $unit_price,
            'total_price' => $unit_price * ($quantity ?? 0),
            'reference' => $finalReference,
            'notes' => $finalNotes,
            'user_id' => auth()->id(),
        ]);

        return $movement;
    }
}

