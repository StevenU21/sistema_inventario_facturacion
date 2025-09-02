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
}
