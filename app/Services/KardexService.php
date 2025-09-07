<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Kardex;
use Carbon\Carbon;

class KardexService
{
    /**
     * Genera el kardex por costo promedio ponderado (CPP)
     *
     * @param int $productId
     * @param int|null $warehouseId
     * @param string|null $from YYYY-MM-DD
     * @param string|null $to YYYY-MM-DD
     * @return array{product:Product, warehouse:Warehouse|null, date_from: string, date_to: string, initial: array{qty:int, unit_cost:float, total:float}, rows: array<int, array>, final: array{qty:int, unit_cost:float, total:float}}
     */
    public function generate(int $productId, ?int $warehouseId = null, ?string $from = null, ?string $to = null): Kardex
    {
        $kardex = new Kardex();
        // Lógica igual que antes, pero asignando atributos al modelo Kardex
        $product = Product::findOrFail($productId);
        $warehouse = $warehouseId ? Warehouse::findOrFail($warehouseId) : null;
        $dateFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::minValue();
        $dateTo = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
        $inventoryIds = Inventory::where('product_id', $productId)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->pluck('id');
        $kardex->product = $product;
        $kardex->warehouse = $warehouse;
        $kardex->date_from = $dateFrom->toDateString();
        $kardex->date_to = $dateTo->toDateString();
        if ($inventoryIds->isEmpty()) {
            $kardex->initial = ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0];
            $kardex->rows = [];
            $kardex->final = ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0];
            return $kardex;
        }
        $previousMovements = InventoryMovement::whereIn('inventory_id', $inventoryIds)
            ->where('created_at', '<', $dateFrom)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $qty = 0;
        $avg = 0.0;
        foreach ($previousMovements as $m) {
            $this->accumulate($m, $qty, $avg);
        }
        $kardex->initial = [
            'qty' => (int) $qty,
            'unit_cost' => round($avg, 2),
            'total' => round($qty * $avg, 2),
        ];
        $movements = InventoryMovement::with(['inventory.warehouse'])
            ->whereIn('inventory_id', $inventoryIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $rows = [];
        $firstInId = null;
        foreach ($movements as $m) {
            $row = [
                'date' => $m->created_at?->format('d/m/Y H:i'),
                'concept' => '',
                'warehouse' => $m->inventory->warehouse->name ?? null,
                'entry_qty' => 0,
                'exit_qty' => 0,
                'balance_qty' => 0,
                'unit_cost' => 0.0,
                'avg_cost' => 0.0,
                'debe' => 0.0,
                'haber' => 0.0,
                'saldo' => 0.0,
                'sale_price' => null,
            ];
            $isInbound = $this->isInbound($m);
            $isOutbound = $this->isOutbound($m);
            if ($isInbound && $firstInId === null) {
                $firstInId = $m->id;
            }
            if ($isInbound) {
                $entryUnit = $m->unit_price > 0 ? (float) $m->unit_price : $avg;
                $entryQty = (int) $m->quantity;
                $entryTotal = $entryQty * $entryUnit;
                $newQty = $qty + $entryQty;
                if ($newQty > 0) {
                    $avg = ($qty * $avg + $entryTotal) / $newQty;
                }
                $qty = $newQty;
                $row['entry_qty'] = $entryQty;
                $row['unit_cost'] = round($entryUnit, 2);
                $row['debe'] = round($entryTotal, 2);
                if ($m->id === $firstInId && $m->type === 'in') {
                    $row['concept'] = 'Inventario Inicial';
                } else {
                    $row['concept'] = 'Entrada';
                }
            } elseif ($isOutbound) {
                $exitQty = (int) $m->quantity;
                // Para Salida, el costo unitario es el costo promedio vigente (CPP)
                $exitUnit = $avg;
                $exitTotal = $exitQty * $exitUnit;
                $qty -= $exitQty;
                $row['exit_qty'] = $exitQty;
                $row['unit_cost'] = round($exitUnit, 2);
                $row['haber'] = round($exitTotal, 2);
                $row['concept'] = 'Salida';
                // Registrar el precio de venta si existe
                $row['sale_price'] = isset($m->sale_price) ? (float)$m->sale_price : null;
            } else {
                continue;
            }
            $row['balance_qty'] = (int) $qty;
            $row['avg_cost'] = round($avg, 2);
            $row['saldo'] = round($qty * $avg, 2);
            $rows[] = $row;
        }
        $kardex->rows = $rows;
        $kardex->final = [
            'qty' => (int) $qty,
            'unit_cost' => round($avg, 2),
            'total' => round($qty * $avg, 2),
        ];
        return $kardex;
    }

    private function concept(InventoryMovement $m): string
    {
        $typeLabel = $m->getMovementTypeAttribute();
        $ref = $m->reference ? " - {$m->reference}" : '';
        return $typeLabel . $ref;
    }

    private function isInbound(InventoryMovement $m): bool
    {
        if ($m->type === 'in')
            return true;
        if ($m->type === 'adjustment' && in_array($m->adjustment_reason, ['correction', 'physical_count']))
            return $m->quantity > 0;
        return false;
    }

    private function isOutbound(InventoryMovement $m): bool
    {
        if ($m->type === 'out' || $m->type === 'transfer')
            return true;
        if ($m->type === 'adjustment' && in_array($m->adjustment_reason, ['damage', 'theft']))
            return $m->quantity > 0;
        return false;
    }

    private function accumulate(InventoryMovement $m, int &$qty, float &$avg): void
    {
        if ($this->isInbound($m)) {
            $entryUnit = $m->unit_price > 0 ? (float) $m->unit_price : $avg;
            $entryQty = (int) $m->quantity;
            $newQty = $qty + $entryQty;
            if ($newQty > 0) {
                $avg = ($qty * $avg + $entryQty * $entryUnit) / $newQty;
            }
            $qty = $newQty;
        } elseif ($this->isOutbound($m)) {
            $qty -= (int) $m->quantity;
            // promedio no cambia
        }
    }
}
