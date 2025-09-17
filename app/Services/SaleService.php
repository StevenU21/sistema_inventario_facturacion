<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Models\AccountReceivable;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleService
{
    public function createSale(array $payload): array
    {
        return DB::transaction(function () use ($payload) {
            $userId = Auth::id();
            $items = $payload['items'] ?? [];
            // Derivar inventario desde la variante de producto; no usar warehouse_id del payload
            $detailsData = $this->calculateDetails($items);
            $totals = $this->calculateTotals($detailsData);

            $sale = $this->createSaleRecord($payload, $userId, $totals);

            foreach ($detailsData as $d) {
                $this->createSaleDetail($sale, $d);
                $this->updateInventory($d['inventory'], $d['quantity']);
                $this->recordInventoryMovement($d['inventory'], $d['quantity'], $sale->id, $userId);
            }

            if ($sale->is_credit) {
                $this->createAccountReceivable($sale);
            }

            $sale->load(['saleDetails.productVariant.product.tax', 'user', 'entity', 'paymentMethod']);
            $pdf = $this->generatePdf($sale);

            return [
                'sale' => $sale,
                'pdf' => $pdf,
            ];
        });
    }

    private function calculateDetails(array $items): array
    {
        $detailsData = [];
        foreach ($items as $row) {
            $variant = ProductVariant::with(['product.tax'])->findOrFail($row['product_variant_id']);
            // Buscar inventario por variante; si el item incluye warehouse_id (derivado automáticamente), filtrarlo
            $rowWarehouseId = $row['warehouse_id'] ?? null;
            $inventory = Inventory::where('product_variant_id', $variant->id)
                ->when($rowWarehouseId, fn($q) => $q->where('warehouse_id', $rowWarehouseId))
                ->lockForUpdate()
                ->first();
            if (!$inventory) {
                throw new \RuntimeException('No hay inventario para la variante seleccionada.');
            }
            $qty = (int) ($row['quantity'] ?? 0);
            $unitSale = (float) ($inventory->sale_price ?? 0);
            $product = $variant->product;
            $tax = $product?->tax;
            $unitTaxAmount = 0.0; // impuesto por unidad
            $taxPercentageApplied = null;
            if ($tax) {
                $percentage = (float) $tax->percentage; // 0 para Exento, 15 para IVA, etc.
                $taxPercentageApplied = $percentage;
                $unitTaxAmount = round($unitSale * ($percentage / 100), 2);
            }
            // Precio unitario mostrado en cliente es sin impuestos (unitSale). Aquí definimos:
            // - base por línea (antes de impuesto) = unitSale * qty - descuento
            // - impuesto por línea = base * %
            // - subtotal por línea = base + impuesto (total con impuesto)
            $hasDiscount = false;
            if (isset($row['discount']) && $row['discount']) {
                $hasDiscount = true;
            }
            $discountAmount = (float) ($row['discount_amount'] ?? 0);
            if (!$hasDiscount) {
                $discountAmount = 0;
            }
            // base antes de impuesto considerando descuento
            $lineBase = max(0, ($unitSale * $qty) - $discountAmount);
            $lineTax = round($lineBase * (($taxPercentageApplied ?? 0) / 100), 2);
            $lineSubtotal = round($lineBase + $lineTax, 2);
            $detailsData[] = [
                'variant' => $variant,
                'inventory' => $inventory,
                'quantity' => $qty,
                // Guardamos unit_price como precio unitario SIN impuesto
                'unit_price' => round($unitSale, 2),
                'sub_total' => $lineSubtotal,
                'discount' => $hasDiscount,
                'discount_amount' => $discountAmount,
                'unit_tax_amount' => $unitTaxAmount,
                'tax_percentage' => $taxPercentageApplied,
            ];
        }
        return $detailsData;
    }

    private function calculateTotals(array $detailsData): array
    {
        $total = 0.0;
        $totalTax = 0.0;
        $taxPercentageApplied = null;
        foreach ($detailsData as $d) {
            $total += $d['sub_total'];
            // Calcular el impuesto sobre el valor con descuento
            $lineBase = max(0, ($d['unit_price'] * $d['quantity']) - $d['discount_amount']);
            $lineTax = round($lineBase * (($d['tax_percentage'] ?? 0) / 100), 2);
            $totalTax += $lineTax;
            if ($d['tax_percentage']) {
                $taxPercentageApplied = $d['tax_percentage'];
            }
        }
        return [
            'total' => round($total, 2),
            'totalTax' => round($totalTax, 2),
            'taxPercentageApplied' => $taxPercentageApplied,
        ];
    }

    private function createSaleRecord(array $payload, $userId, array $totals)
    {
        return Sale::create([
            'total' => $totals['total'],
            'is_credit' => (bool) ($payload['is_credit'] ?? false),
            'tax_percentage' => $totals['taxPercentageApplied'],
            'tax_amount' => $totals['totalTax'],
            'sale_date' => $payload['sale_date'] ?? now()->toDateString(),
            'user_id' => $userId,
            'entity_id' => $payload['entity_id'],
            'payment_method_id' => $payload['payment_method_id'] ?? null,
        ]);
    }

    private function createSaleDetail($sale, array $d)
    {
        $sale->saleDetails()->create([
            'quantity' => $d['quantity'],
            'unit_price' => $d['unit_price'],
            'sub_total' => $d['sub_total'],
            'discount' => $d['discount'],
            'discount_amount' => $d['discount_amount'],
            'product_variant_id' => $d['variant']->id,
        ]);
    }

    private function updateInventory($inventory, $quantity)
    {
        $inventory->stock -= $quantity;
        $inventory->save();
    }

    private function recordInventoryMovement($inventory, $quantity, $saleId, $userId)
    {
        $inventory->inventoryMovements()->create([
            'type' => 'out',
            'quantity' => $quantity,
            'unit_price' => $inventory->purchase_price ?? 0,
            'total_price' => ($inventory->purchase_price ?? 0) * $quantity,
            'reference' => 'Venta #' . $saleId,
            'notes' => 'Salida de inventario por venta',
            'user_id' => $userId,
        ]);
    }

    private function createAccountReceivable($sale)
    {
        AccountReceivable::create([
            'amount_due' => $sale->total,
            'amount_paid' => 0,
            'status' => 'pending',
            'entity_id' => $sale->entity_id,
            'sale_id' => $sale->id,
        ]);
    }

    private function generatePdf($sale)
    {
        // Ensure AR relation is available for invoice view
        $sale->loadMissing(['accountReceivable']);
        return Pdf::loadView('cashier.sales.invoice', [
            'sale' => $sale,
            'company' => Company::first(),
            'details' => $sale->saleDetails,
        ])->setPaper('letter');
    }

    /**
     * Create a Sale record from a given Quotation.
     */
    public function createSaleFromQuotation(\App\Models\Quotation $quotation): Sale
    {
        // Prepare payload from quotation details
        $items = $quotation->QuotationDetails->map(function($d) {
            return [
                'product_variant_id' => $d->product_variant_id,
                'quantity' => $d->quantity,
                'discount' => $d->discount,
                'discount_amount' => $d->discount_amount,
                // warehouse derivado automáticamente en la etapa de cotización
                'warehouse_id' => $d->warehouse_id,
            ];
        })->toArray();

        $payload = [
            'items' => $items,
            'entity_id' => $quotation->entity_id,
            'sale_date' => now()->toDateString(),
            // is_credit, warehouse_id, payment_method_id can be set as needed
        ];

        $result = $this->createSale($payload);
        $sale = $result['sale'];

        // Link sale to quotation
        $sale->quotation_id = $quotation->id;
        $sale->save();

        return $sale;
    }
}
