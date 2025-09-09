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
    /**
     * @param string $metodo 'cpp'|'peps'|'ueps'
     */
    public function generate(int $productId, ?int $warehouseId = null, ?string $from = null, ?string $to = null, string $metodo = 'cpp', ?int $colorId = null, ?int $sizeId = null): Kardex
    {
        $kardex = new Kardex();
        // Lógica igual que antes, pero asignando atributos al modelo Kardex
        $product = Product::findOrFail($productId);
        $warehouse = $warehouseId ? Warehouse::findOrFail($warehouseId) : null;
        $dateFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::minValue();
        $dateTo = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
        // Ajustado a la nueva lógica: inventarios se relacionan por variante de producto.
        // Traer todos los inventarios cuyas variantes pertenezcan al producto padre seleccionado.
        $inventoryIds = Inventory::whereHas('productVariant', function ($q) use ($productId, $colorId, $sizeId) {
            $q->where('product_id', $productId);
            if (!is_null($colorId)) {
                $q->where('color_id', $colorId);
            }
            if (!is_null($sizeId)) {
                $q->where('size_id', $sizeId);
            }
        })
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
        // Variables para los métodos
        $qty = 0;
        $avg = 0.0;
        $fifoStack = [];
        $lifoStack = [];

        // Calcular saldo inicial según método
        if ($metodo === 'cpp') {
            foreach ($previousMovements as $m) {
                $this->accumulate($m, $qty, $avg);
            }
            $kardex->initial = [
                'qty' => (int) $qty,
                'unit_cost' => round($avg, 2),
                'total' => round($qty * $avg, 2),
            ];
        } elseif ($metodo === 'peps') {
            $qty = 0;
            foreach ($previousMovements as $m) {
                if ($this->isInbound($m)) {
                    $fifoStack[] = [
                        'qty' => (int) $m->quantity,
                        'unit_cost' => (float) $m->unit_price
                    ];
                    $qty += (int) $m->quantity;
                } elseif ($this->isOutbound($m)) {
                    $outQty = (int) $m->quantity;
                    while ($outQty > 0 && count($fifoStack) > 0) {
                        $lote = &$fifoStack[0];
                        if ($lote['qty'] > $outQty) {
                            $lote['qty'] -= $outQty;
                            $qty -= $outQty;
                            $outQty = 0;
                        } else {
                            $qty -= $lote['qty'];
                            $outQty -= $lote['qty'];
                            array_shift($fifoStack);
                        }
                    }
                }
            }
            $unit_cost = 0.0;
            $total = 0.0;
            foreach ($fifoStack as $lote) {
                $total += $lote['qty'] * $lote['unit_cost'];
            }
            $unit_cost = $qty > 0 ? $total / $qty : 0.0;
            $kardex->initial = [
                'qty' => (int) $qty,
                'unit_cost' => round($unit_cost, 2),
                'total' => round($total, 2),
            ];
        } elseif ($metodo === 'ueps') {
            $qty = 0;
            foreach ($previousMovements as $m) {
                if ($this->isInbound($m)) {
                    $lifoStack[] = [
                        'qty' => (int) $m->quantity,
                        'unit_cost' => (float) $m->unit_price
                    ];
                    $qty += (int) $m->quantity;
                } elseif ($this->isOutbound($m)) {
                    $outQty = (int) $m->quantity;
                    while ($outQty > 0 && count($lifoStack) > 0) {
                        $lote = &$lifoStack[count($lifoStack) - 1];
                        if ($lote['qty'] > $outQty) {
                            $lote['qty'] -= $outQty;
                            $qty -= $outQty;
                            $outQty = 0;
                        } else {
                            $qty -= $lote['qty'];
                            $outQty -= $lote['qty'];
                            array_pop($lifoStack);
                        }
                    }
                }
            }
            $unit_cost = 0.0;
            $total = 0.0;
            foreach ($lifoStack as $lote) {
                $total += $lote['qty'] * $lote['unit_cost'];
            }
            $unit_cost = $qty > 0 ? $total / $qty : 0.0;
            $kardex->initial = [
                'qty' => (int) $qty,
                'unit_cost' => round($unit_cost, 2),
                'total' => round($total, 2),
            ];
        }
        $movements = InventoryMovement::with(['inventory.warehouse'])
            ->whereIn('inventory_id', $inventoryIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $rows = [];
        $firstInId = null;
        // Variables para PEPS y UEPS
        $fifoStackMov = $fifoStack;
        $lifoStackMov = $lifoStack;
        $qtyMov = $qty;
        $avgMov = $avg;
        foreach ($movements as $m) {
            $row = [
                'date' => $m->created_at?->format('d/m/Y'),
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
            if ($metodo === 'cpp') {
                if ($isInbound) {
                    $entryUnit = $m->unit_price > 0 ? (float) $m->unit_price : $avgMov;
                    $entryQty = (int) $m->quantity;
                    $entryTotal = $entryQty * $entryUnit;
                    $newQty = $qtyMov + $entryQty;
                    if ($newQty > 0) {
                        $avgMov = ($qtyMov * $avgMov + $entryTotal) / $newQty;
                    }
                    $qtyMov = $newQty;
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
                    $exitUnit = $avgMov;
                    $exitTotal = $exitQty * $exitUnit;
                    $qtyMov -= $exitQty;
                    $row['exit_qty'] = $exitQty;
                    $row['unit_cost'] = round($exitUnit, 2);
                    $row['haber'] = round($exitTotal, 2);
                    $row['concept'] = 'Salida';
                    $row['sale_price'] = isset($m->sale_price) ? (float) $m->sale_price : null;
                } else {
                    continue;
                }
                $row['balance_qty'] = (int) $qtyMov;
                $row['avg_cost'] = round($avgMov, 2);
                $row['saldo'] = round($qtyMov * $avgMov, 2);
            } elseif ($metodo === 'peps') {
                if ($isInbound) {
                    $fifoStackMov[] = [
                        'qty' => (int) $m->quantity,
                        'unit_cost' => (float) $m->unit_price
                    ];
                    $qtyMov += (int) $m->quantity;
                    $row['entry_qty'] = (int) $m->quantity;
                    $row['unit_cost'] = round((float) $m->unit_price, 2);
                    $row['debe'] = round($m->quantity * (float) $m->unit_price, 2);
                    $row['concept'] = 'Entrada';
                } elseif ($isOutbound) {
                    $exitQty = (int) $m->quantity;
                    $exitTotal = 0.0;
                    $exitUnit = 0.0;
                    $qtyToRemove = $exitQty;
                    $costos = [];
                    while ($qtyToRemove > 0 && count($fifoStackMov) > 0) {
                        $lote = &$fifoStackMov[0];
                        $used = min($lote['qty'], $qtyToRemove);
                        $costos[] = ['qty' => $used, 'unit_cost' => $lote['unit_cost']];
                        $exitTotal += $used * $lote['unit_cost'];
                        $qtyToRemove -= $used;
                        $lote['qty'] -= $used;
                        if ($lote['qty'] == 0)
                            array_shift($fifoStackMov);
                    }
                    $qtyMov -= $exitQty;
                    $exitUnit = $exitQty > 0 ? $exitTotal / $exitQty : 0.0;
                    $row['exit_qty'] = $exitQty;
                    $row['unit_cost'] = round($exitUnit, 2);
                    $row['haber'] = round($exitTotal, 2);
                    $row['concept'] = 'Salida';
                    $row['sale_price'] = isset($m->sale_price) ? (float) $m->sale_price : null;
                } else {
                    continue;
                }
                $row['balance_qty'] = (int) $qtyMov;
                // Calcular costo promedio actual de los lotes restantes
                $totalRestante = 0.0;
                foreach ($fifoStackMov as $lote) {
                    $totalRestante += $lote['qty'] * $lote['unit_cost'];
                }
                $row['avg_cost'] = $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0;
                $row['saldo'] = round($totalRestante, 2);
            } elseif ($metodo === 'ueps') {
                if ($isInbound) {
                    $lifoStackMov[] = [
                        'qty' => (int) $m->quantity,
                        'unit_cost' => (float) $m->unit_price
                    ];
                    $qtyMov += (int) $m->quantity;
                    $row['entry_qty'] = (int) $m->quantity;
                    $row['unit_cost'] = round((float) $m->unit_price, 2);
                    $row['debe'] = round($m->quantity * (float) $m->unit_price, 2);
                    $row['concept'] = 'Entrada';
                } elseif ($isOutbound) {
                    $exitQty = (int) $m->quantity;
                    $exitTotal = 0.0;
                    $exitUnit = 0.0;
                    $qtyToRemove = $exitQty;
                    $costos = [];
                    while ($qtyToRemove > 0 && count($lifoStackMov) > 0) {
                        $lote = &$lifoStackMov[count($lifoStackMov) - 1];
                        $used = min($lote['qty'], $qtyToRemove);
                        $costos[] = ['qty' => $used, 'unit_cost' => $lote['unit_cost']];
                        $exitTotal += $used * $lote['unit_cost'];
                        $qtyToRemove -= $used;
                        $lote['qty'] -= $used;
                        if ($lote['qty'] == 0)
                            array_pop($lifoStackMov);
                    }
                    $qtyMov -= $exitQty;
                    $exitUnit = $exitQty > 0 ? $exitTotal / $exitQty : 0.0;
                    $row['exit_qty'] = $exitQty;
                    $row['unit_cost'] = round($exitUnit, 2);
                    $row['haber'] = round($exitTotal, 2);
                    $row['concept'] = 'Salida';
                    $row['sale_price'] = isset($m->sale_price) ? (float) $m->sale_price : null;
                } else {
                    continue;
                }
                $row['balance_qty'] = (int) $qtyMov;
                // Calcular costo promedio actual de los lotes restantes
                $totalRestante = 0.0;
                foreach ($lifoStackMov as $lote) {
                    $totalRestante += $lote['qty'] * $lote['unit_cost'];
                }
                $row['avg_cost'] = $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0;
                $row['saldo'] = round($totalRestante, 2);
            }
            $rows[] = $row;
        }
        $kardex->rows = $rows;
        // Calcular final según método
        if ($metodo === 'cpp') {
            $kardex->final = [
                'qty' => (int) $qtyMov,
                'unit_cost' => round($avgMov, 2),
                'total' => round($qtyMov * $avgMov, 2),
            ];
        } elseif ($metodo === 'peps') {
            $totalRestante = 0.0;
            foreach ($fifoStackMov as $lote) {
                $totalRestante += $lote['qty'] * $lote['unit_cost'];
            }
            $kardex->final = [
                'qty' => (int) $qtyMov,
                'unit_cost' => $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0,
                'total' => round($totalRestante, 2),
            ];
        } elseif ($metodo === 'ueps') {
            $totalRestante = 0.0;
            foreach ($lifoStackMov as $lote) {
                $totalRestante += $lote['qty'] * $lote['unit_cost'];
            }
            $kardex->final = [
                'qty' => (int) $qtyMov,
                'unit_cost' => $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0,
                'total' => round($totalRestante, 2),
            ];
        }
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
