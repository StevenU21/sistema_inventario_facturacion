<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
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
    public function generate(int $productId, ?int $warehouseId = null, ?string $from = null, ?string $to = null): array
    {
        $product = Product::findOrFail($productId);
        $warehouse = $warehouseId ? Warehouse::findOrFail($warehouseId) : null;

        $dateFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::minValue();
        $dateTo = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();

        $inventoryIds = Inventory::where('product_id', $productId)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->pluck('id');

        // No hay inventarios => retorno vacío
        if ($inventoryIds->isEmpty()) {
            return [
                'product' => $product,
                'warehouse' => $warehouse,
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'initial' => ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0],
                'rows' => [],
                'final' => ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0],
            ];
        }

        // Movimientos previos al rango para calcular saldo inicial
        $previousMovements = InventoryMovement::whereIn('inventory_id', $inventoryIds)
            ->where('created_at', '<', $dateFrom)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $qty = 0;
        $avg = 0.0; // costo promedio

        foreach ($previousMovements as $m) {
            $this->accumulate($m, $qty, $avg);
        }

        $initial = [
            'qty' => (int) $qty,
            'unit_cost' => round($avg, 2),
            'total' => round($qty * $avg, 2),
        ];

        // Movimientos dentro del rango
        $movements = InventoryMovement::with(['inventory.warehouse'])
            ->whereIn('inventory_id', $inventoryIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $rows = [];
    // Ya no se agrega fila de saldo inicial vacía; solo movimientos reales
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
            ];

            $isInbound = $this->isInbound($m);
            $isOutbound = $this->isOutbound($m);

            // Detectar el primer movimiento de entrada para el producto
            if ($isInbound && $firstInId === null) {
                $firstInId = $m->id;
            }

            if ($isInbound) {
                // Para Inventario Inicial y Entrada, el costo unitario es el precio de compra (unit_price)
                $entryUnit = $m->unit_price > 0 ? (float) $m->unit_price : $avg;
                $entryQty = (int) $m->quantity;
                $entryTotal = $entryQty * $entryUnit;

                $newQty = $qty + $entryQty;
                if ($newQty > 0) {
                    $avg = ($qty * $avg + $entryTotal) / $newQty;
                }
                $qty = $newQty;

                $row['entry_qty'] = $entryQty;
                $row['unit_cost'] = round($entryUnit, 2); // precio de compra
                $row['debe'] = round($entryTotal, 2);
                // Concepto: Inventario Inicial solo para el primer movimiento de entrada
                if ($m->id === $firstInId && $m->type === 'in') {
                    $row['concept'] = 'Inventario Inicial';
                } else {
                    $row['concept'] = 'Entrada';
                }
            } elseif ($isOutbound) {
                $exitQty = (int) $m->quantity;
                // Para Salida, el costo unitario es SIEMPRE el sale_price del inventario relacionado (para depuración)
                $exitUnit = isset($m->inventory->sale_price) ? (float) $m->inventory->sale_price : 0.0;
                $exitTotal = $exitQty * $exitUnit;
                $qty -= $exitQty;

                $row['exit_qty'] = $exitQty;
                $row['unit_cost'] = round($exitUnit, 2); // precio de venta (forzado)
                $row['haber'] = round($exitTotal, 2);
                $row['concept'] = 'Salida';
            } else {
                // Ajustes de precio u otros sin cantidad: no afectan saldo ni concepto
                continue;
            }

            $row['balance_qty'] = (int) $qty;
            $row['avg_cost'] = round($avg, 2);
            $row['saldo'] = round($qty * $avg, 2);

            $rows[] = $row;
        }

        $final = [
            'qty' => (int) $qty,
            'unit_cost' => round($avg, 2),
            'total' => round($qty * $avg, 2),
        ];

        return [
            'product' => $product,
            'warehouse' => $warehouse,
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'initial' => $initial,
            'rows' => $rows,
            'final' => $final,
        ];
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
