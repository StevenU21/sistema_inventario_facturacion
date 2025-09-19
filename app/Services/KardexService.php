<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductVariant;
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
    /**
     * Genera kardex.
     * Prioriza variante específica si se proporciona $productVariantId; en ese caso se ignoran colorId/sizeId porque ya pertenecen a la variante.
     * Mantiene compatibilidad retro: llamadas antiguas sin variant siguen funcionando por producto + color/talla.
     */
    public function generate(int $productId, ?int $warehouseId = null, ?string $from = null, ?string $to = null, string $metodo = 'cpp', ?int $colorId = null, ?int $sizeId = null, ?int $productVariantId = null): Kardex
    {
        $kardex = new Kardex();

        [$product, $variant, $warehouse] = $this->findProductVariantAndWarehouse($productId, $productVariantId, $warehouseId);
        [$dateFrom, $dateTo] = $this->resolveDateRange($from, $to);
        $inventoryIds = $this->getInventoryIds($productId, $warehouseId, $colorId, $sizeId, $productVariantId);

        $kardex->product = $product;
        $kardex->variant = $variant;
        $kardex->warehouse = $warehouse;
        $kardex->date_from = $dateFrom->toDateString();
        $kardex->date_to = $dateTo->toDateString();

        if ($inventoryIds->isEmpty()) {
            $this->setEmptyResult($kardex);
            return $kardex;
        }

        $previousMovements = $this->getPreviousMovements($inventoryIds, $dateFrom);
        [$initial, $state] = $this->computeInitialState($previousMovements, $metodo);
        $kardex->initial = $initial;

        $movements = $this->getPeriodMovements($inventoryIds, $dateFrom, $dateTo);
        [$rows, $final] = $this->processMovements($movements, $metodo, $state);
        $kardex->rows = $rows;
        $kardex->final = $final;

        return $kardex;
    }

    /**
     * Resuelve fechas de rango.
     * @return array{0:Carbon,1:Carbon}
     */
    private function resolveDateRange(?string $from, ?string $to): array
    {
        $dateFrom = $from ? Carbon::parse($from)->startOfDay() : Carbon::minValue();
        $dateTo = $to ? Carbon::parse($to)->endOfDay() : Carbon::now()->endOfDay();
        return [$dateFrom, $dateTo];
    }

    /**
     * Obtiene producto y almacén (si aplica).
     * @return array{0:Product,1:Warehouse|null}
     */
    /**
     * Obtiene producto, variante (si aplica) y almacén.
     * @return array{0:Product,1:ProductVariant|null,2:Warehouse|null}
     */
    private function findProductVariantAndWarehouse(int $productId, ?int $productVariantId, ?int $warehouseId): array
    {
        $product = Product::findOrFail($productId);
        $variant = null;
        if ($productVariantId) {
            $variant = ProductVariant::where('product_id', $productId)->findOrFail($productVariantId);
        }
        $warehouse = $warehouseId ? Warehouse::findOrFail($warehouseId) : null;
        return [$product, $variant, $warehouse];
    }

    /**
     * Obtiene IDs de inventarios filtrando por variantes del producto y opcionalmente almacén/color/talla.
     */
    private function getInventoryIds(int $productId, ?int $warehouseId, ?int $colorId, ?int $sizeId, ?int $productVariantId)
    {
        // Si se especifica la variante, filtramos directamente por ella y omitimos color/talla (ya pertenecen a la variante)
        if ($productVariantId) {
            return Inventory::where('product_variant_id', $productVariantId)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
                ->pluck('id');
        }

        // Modo anterior (por producto + filtros color/talla)
        return Inventory::whereHas('productVariant', function ($q) use ($productId, $colorId, $sizeId) {
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
    }

    /**
     * Movimientos previos al rango (para saldo inicial).
     */
    private function getPreviousMovements($inventoryIds, Carbon $dateFrom)
    {
        return InventoryMovement::whereIn('inventory_id', $inventoryIds)
            ->where('created_at', '<', $dateFrom)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * Movimientos dentro del rango solicitado.
     */
    private function getPeriodMovements($inventoryIds, Carbon $dateFrom, Carbon $dateTo)
    {
        return InventoryMovement::with(['inventory.warehouse'])
            ->whereIn('inventory_id', $inventoryIds)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    /**
     * Calcula el saldo inicial según el método y devuelve también el estado interno requerido para procesar los movimientos.
     * @return array{0:array{qty:int,unit_cost:float,total:float},1:array{qty:int,avg:float,fifo:array<int,array{qty:int,unit_cost:float}>,lifo:array<int,array{qty:int,unit_cost:float}>}}
     */
    private function computeInitialState($previousMovements, string $metodo): array
    {
        $qty = 0;
        $avg = 0.0;
        $fifoStack = [];
        $lifoStack = [];

        if ($metodo === 'cpp') {
            foreach ($previousMovements as $m) {
                $this->accumulate($m, $qty, $avg);
            }
            $initial = [
                'qty' => (int) $qty,
                'unit_cost' => round($avg, 2),
                'total' => round($qty * $avg, 2),
            ];
        } elseif ($metodo === 'peps') {
            foreach ($previousMovements as $m) {
                if ($this->isInbound($m)) {
                    $fifoStack[] = [
                        'qty' => (int) $m->quantity,
                        'unit_cost' => (float) $m->unit_price,
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
            $total = 0.0;
            foreach ($fifoStack as $lote) {
                $total += $lote['qty'] * $lote['unit_cost'];
            }
            $unit_cost = $qty > 0 ? $total / $qty : 0.0;
            $initial = [
                'qty' => (int) $qty,
                'unit_cost' => round($unit_cost, 2),
                'total' => round($total, 2),
            ];
        } elseif ($metodo === 'ueps') {
            foreach ($previousMovements as $m) {
                if ($this->isInbound($m)) {
                    $lifoStack[] = [
                        'qty' => (int) $m->quantity,
                        'unit_cost' => (float) $m->unit_price,
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
            $total = 0.0;
            foreach ($lifoStack as $lote) {
                $total += $lote['qty'] * $lote['unit_cost'];
            }
            $unit_cost = $qty > 0 ? $total / $qty : 0.0;
            $initial = [
                'qty' => (int) $qty,
                'unit_cost' => round($unit_cost, 2),
                'total' => round($total, 2),
            ];
        } else {
            // Método desconocido: devolver vacío seguro
            $initial = ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0];
        }

        $state = [
            'qty' => $qty,
            'avg' => $avg,
            'fifo' => $fifoStack,
            'lifo' => $lifoStack,
        ];

        return [$initial, $state];
    }

    /**
     * Procesa movimientos del periodo y calcula filas y saldo final.
     * @param string $metodo 'cpp'|'peps'|'ueps'
     * @param array $state Estado proveniente de computeInitialState
     * @return array{0:array<int,array>,1:array{qty:int,unit_cost:float,total:float}}
     */
    private function processMovements($movements, string $metodo, array $state): array
    {
        $rows = [];
        $firstInId = null;
        $qtyMov = $state['qty'];
        $avgMov = $state['avg'];
        $fifoStackMov = $state['fifo'];
        $lifoStackMov = $state['lifo'];

        foreach ($movements as $m) {
            $row = $this->baseRow($m);
            // Nuevos campos opcionales para análisis de ventas
            $row['revenue'] = 0.0;  // venta bruta (precio venta * cantidad)
            $row['profit'] = 0.0;   // utilidad (revenue - costo)

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
                    $row['concept'] = ($m->id === $firstInId && $m->type === 'in') ? 'Inventario Inicial' : $this->concept($m);
                } elseif ($isOutbound) {
                    $exitQty = (int) $m->quantity;
                    $exitUnit = $avgMov;
                    $exitTotal = $exitQty * $exitUnit;
                    $qtyMov -= $exitQty;
                    $row['exit_qty'] = $exitQty;
                    $row['unit_cost'] = round($exitUnit, 2);
                    $row['haber'] = round($exitTotal, 2);
                    $row['concept'] = $this->concept($m);
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
                        'unit_cost' => (float) $m->unit_price,
                    ];
                    $qtyMov += (int) $m->quantity;
                    $row['entry_qty'] = (int) $m->quantity;
                    $row['unit_cost'] = round((float) $m->unit_price, 2);
                    $row['debe'] = round($m->quantity * (float) $m->unit_price, 2);
                    $row['concept'] = ($m->id === $firstInId && $m->type === 'in') ? 'Inventario Inicial' : $this->concept($m);
                } elseif ($isOutbound) {
                    $exitQty = (int) $m->quantity;
                    $exitTotal = 0.0;
                    $qtyToRemove = $exitQty;
                    while ($qtyToRemove > 0 && count($fifoStackMov) > 0) {
                        $lote = &$fifoStackMov[0];
                        $used = min($lote['qty'], $qtyToRemove);
                        $exitTotal += $used * $lote['unit_cost'];
                        $qtyToRemove -= $used;
                        $lote['qty'] -= $used;
                        if ($lote['qty'] == 0) {
                            array_shift($fifoStackMov);
                        }
                    }
                    $qtyMov -= $exitQty;
                    $exitUnit = $exitQty > 0 ? $exitTotal / $exitQty : 0.0;
                    $row['exit_qty'] = $exitQty;
                    $row['unit_cost'] = round($exitUnit, 2);
                    $row['haber'] = round($exitTotal, 2);
                    $row['concept'] = $this->concept($m);
                    $row['sale_price'] = isset($m->sale_price) ? (float) $m->sale_price : null;
                } else {
                    continue;
                }
                $row['balance_qty'] = (int) $qtyMov;
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
                        'unit_cost' => (float) $m->unit_price,
                    ];
                    $qtyMov += (int) $m->quantity;
                    $row['entry_qty'] = (int) $m->quantity;
                    $row['unit_cost'] = round((float) $m->unit_price, 2);
                    $row['debe'] = round($m->quantity * (float) $m->unit_price, 2);
                    $row['concept'] = ($m->id === $firstInId && $m->type === 'in') ? 'Inventario Inicial' : $this->concept($m);
                } elseif ($isOutbound) {
                    $exitQty = (int) $m->quantity;
                    $exitTotal = 0.0;
                    $qtyToRemove = $exitQty;
                    while ($qtyToRemove > 0 && count($lifoStackMov) > 0) {
                        $lote = &$lifoStackMov[count($lifoStackMov) - 1];
                        $used = min($lote['qty'], $qtyToRemove);
                        $exitTotal += $used * $lote['unit_cost'];
                        $qtyToRemove -= $used;
                        $lote['qty'] -= $used;
                        if ($lote['qty'] == 0) {
                            array_pop($lifoStackMov);
                        }
                    }
                    $qtyMov -= $exitQty;
                    $exitUnit = $exitQty > 0 ? $exitTotal / $exitQty : 0.0;
                    $row['exit_qty'] = $exitQty;
                    $row['unit_cost'] = round($exitUnit, 2);
                    $row['haber'] = round($exitTotal, 2);
                    $row['concept'] = $this->concept($m);
                    $row['sale_price'] = isset($m->sale_price) ? (float) $m->sale_price : null;
                } else {
                    continue;
                }
                $row['balance_qty'] = (int) $qtyMov;
                $totalRestante = 0.0;
                foreach ($lifoStackMov as $lote) {
                    $totalRestante += $lote['qty'] * $lote['unit_cost'];
                }
                $row['avg_cost'] = $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0;
                $row['saldo'] = round($totalRestante, 2);
            } else {
                continue; // método desconocido
            }

            // Calcular revenue/profit solo para salidas de venta (cuando hay sale_price)
            if ($isOutbound && isset($row['sale_price']) && $row['sale_price'] !== null) {
                $row['revenue'] = round($row['sale_price'] * $row['exit_qty'], 2);
                // Costo ya calculado en 'haber'
                $row['profit'] = round($row['revenue'] - $row['haber'], 2);
            }

            $rows[] = $row;
        }

        // Calcular final según método
        if ($metodo === 'cpp') {
            $final = [
                'qty' => (int) $qtyMov,
                'unit_cost' => round($avgMov, 2),
                'total' => round($qtyMov * $avgMov, 2),
            ];
        } elseif ($metodo === 'peps') {
            $totalRestante = 0.0;
            foreach ($fifoStackMov as $lote) {
                $totalRestante += $lote['qty'] * $lote['unit_cost'];
            }
            $final = [
                'qty' => (int) $qtyMov,
                'unit_cost' => $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0,
                'total' => round($totalRestante, 2),
            ];
        } elseif ($metodo === 'ueps') {
            $totalRestante = 0.0;
            foreach ($lifoStackMov as $lote) {
                $totalRestante += $lote['qty'] * $lote['unit_cost'];
            }
            $final = [
                'qty' => (int) $qtyMov,
                'unit_cost' => $qtyMov > 0 ? round($totalRestante / $qtyMov, 2) : 0.0,
                'total' => round($totalRestante, 2),
            ];
        } else {
            $final = ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0];
        }

        return [$rows, $final];
    }

    /**
     * Setea resultado vacío cuando no hay inventarios relacionados.
     */
    private function setEmptyResult(Kardex $kardex): void
    {
        $kardex->initial = ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0];
        $kardex->rows = [];
        $kardex->final = ['qty' => 0, 'unit_cost' => 0.0, 'total' => 0.0];
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
        // Devoluciones (return) incrementan inventario
        if ($m->type === 'return')
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

    /**
     * Construye la estructura base de una fila del kardex para un movimiento.
     */
    private function baseRow(InventoryMovement $m): array
    {
        return [
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
            'sale_price' => isset($m->sale_price) ? (float) $m->sale_price : null,
        ];
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
