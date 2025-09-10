<?php

namespace App\Services;

use App\Http\Requests\PurchaseDetailRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Inventory;
use App\Models\InventoryMovement;

class PurchaseService
{
    public function addDetail(PurchaseDetailRequest $request, Purchase $purchase)
    {
        $data = $request->validated();
        $data['purchase_id'] = $purchase->id;
        $detail = PurchaseDetail::create($data);
        $this->applyDetailToInventory($purchase, $detail);
        $this->recalculateTotals($purchase);
        return $detail;
    }

    public function removeDetail(Purchase $purchase, PurchaseDetail $detail)
    {
        if ($detail->purchase_id !== $purchase->id) {
            abort(404);
        }
        $this->revertDetailFromInventory($purchase, $detail);
        $detail->delete();
        $this->recalculateTotals($purchase);
    }

    public function applyDetailToInventory(Purchase $purchase, PurchaseDetail $detail): void
    {
        $inventory = Inventory::firstOrCreate(
            [
                'product_variant_id' => $detail->product_variant_id,
                'warehouse_id' => $purchase->warehouse_id,
            ],
            [
                'stock' => 0,
                'min_stock' => 0,
                'purchase_price' => $detail->unit_price,
                'sale_price' => round($detail->unit_price * 1.3, 2),
            ]
        );
        $inventory->stock += $detail->quantity;
        $inventory->purchase_price = $detail->unit_price;
        $inventory->save();
        InventoryMovement::create([
            'type' => 'in',
            'adjustment_reason' => null,
            'quantity' => $detail->quantity,
            'unit_price' => $detail->unit_price,
            'total_price' => $detail->quantity * $detail->unit_price,
            'reference' => $purchase->reference,
            'notes' => 'Entrada por compra (CRUD)',
            'user_id' => auth()->id(),
            'inventory_id' => $inventory->id,
        ]);
    }

    public function revertDetailFromInventory(Purchase $purchase, PurchaseDetail $detail): void
    {
        $inventory = Inventory::where('product_variant_id', $detail->product_variant_id)
            ->where('warehouse_id', $purchase->warehouse_id)
            ->first();
        if (!$inventory)
            return;
        $inventory->stock = max(0, $inventory->stock - $detail->quantity);
        $inventory->save();
        InventoryMovement::create([
            'type' => 'out',
            'adjustment_reason' => null,
            'quantity' => $detail->quantity,
            'unit_price' => $detail->unit_price,
            'total_price' => $detail->quantity * $detail->unit_price,
            'reference' => $purchase->reference,
            'notes' => 'Reversión de detalle de compra (CRUD)',
            'user_id' => auth()->id(),
            'inventory_id' => $inventory->id,
        ]);
    }

    public function recalculateTotals(Purchase $purchase): void
    {
        $subtotal = PurchaseDetail::where('purchase_id', $purchase->id)
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as subtotal')
            ->value('subtotal');
        $purchase->subtotal = $subtotal;
        $purchase->total = $subtotal;
        $purchase->save();
    }
}
