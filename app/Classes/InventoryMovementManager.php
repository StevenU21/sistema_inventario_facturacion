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
        $unit_price = $request->input('unit_price', null);
        $reference = $request->input('reference');
        $notes = $request->input('notes');

        \DB::beginTransaction();
        try {
            $destInventory = Inventory::where('product_id', $inventory->product_id)
                ->where('warehouse_id', $destWarehouseId)
                ->first();


            if ($destInventory && $destInventory->id == $inventory->id) {
                \DB::rollBack();
                return ['error' => ['destination_warehouse_id' => 'El producto ya existe en el almacén seleccionado.']];
            }


            $inventory->stock -= $quantity;
            $inventory->save();
            $transferMovement = $inventory->inventoryMovements()->create([
                'type' => 'transfer',
                'quantity' => $quantity,
                'unit_price' => $unit_price !== null ? $unit_price : $inventory->purchase_price,
                'total_price' => ($unit_price !== null ? $unit_price : $inventory->purchase_price) * $quantity,
                'reference' => $reference,
                'notes' => $notes ?? 'Transferencia a almacén destino',
                'user_id' => auth()->id(),
            ]);

            if (!$destInventory) {
                $destInventory = Inventory::create([
                    'product_id' => $inventory->product_id,
                    'warehouse_id' => $destWarehouseId,
                    'stock' => $quantity,
                    'min_stock' => $inventory->min_stock,
                    'purchase_price' => $unit_price !== null ? $unit_price : $inventory->purchase_price,
                    'sale_price' => $inventory->sale_price,
                ]);
            } else {
                $destInventory->stock += $quantity;
                if ($unit_price !== null) {
                    $destInventory->purchase_price = $unit_price;
                }
                $destInventory->save();
            }

            $destInventory->inventoryMovements()->create([
                'type' => 'in',
                'quantity' => $quantity,
                'unit_price' => $unit_price !== null ? $unit_price : $destInventory->purchase_price,
                'total_price' => ($unit_price !== null ? $unit_price : $destInventory->purchase_price) * $quantity,
                'reference' => $reference,
                'notes' => $notes ? $notes : 'Transferido desde almacén origen',
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
