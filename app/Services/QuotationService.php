<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Entity;
use App\Models\Inventory;
use App\Models\ProductVariant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class QuotationService
{
    /**
     * Genera una proforma (sin persistir en BD) y devuelve el PDF y datos calculados.
     *
     * Payload esperado:
     * - entity_id: int
     * - warehouse_id: int
     * - quotation_date?: Y-m-d
     * - items: [ { product_variant_id:int, quantity:int, discount?:bool, discount_amount?:float } ]
     *
     * @return array{pdf: \Barryvdh\DomPDF\PDF, details: array, totals: array}
     */
    public function createQuotation(array $payload): array
    {
        $items = $payload['items'] ?? [];
        $warehouseId = $payload['warehouse_id'];

        $details = $this->calculateDetails($items, $warehouseId);
        $totals = $this->calculateTotals($details);

        $entity = Entity::find($payload['entity_id']);
        $quotationDate = $payload['quotation_date'] ?? now()->toDateString();

        $pdf = $this->generatePdf($entity, $details, $totals, $quotationDate);

        return [
            'pdf' => $pdf,
            'details' => $details,
            'totals' => $totals,
        ];
    }

    private function calculateDetails(array $items, int $warehouseId): array
    {
        $details = [];
        foreach ($items as $row) {
            $variant = ProductVariant::with(['product.tax'])->findOrFail($row['product_variant_id']);
            $inventory = Inventory::where('product_variant_id', $variant->id)
                ->where('warehouse_id', $warehouseId)
                ->first();

            $qty = (int) ($row['quantity'] ?? 0);
            $unitSale = (float) ($inventory->sale_price ?? 0);
            $tax = $variant->product?->tax;
            $unitTaxAmount = 0.0;
            $taxPercentageApplied = null;
            if ($tax) {
                $percentage = (float) $tax->percentage;
                $taxPercentageApplied = $percentage;
                $unitTaxAmount = round($unitSale * ($percentage / 100), 2);
            }

            $unitPriceWithTax = $unitSale + $unitTaxAmount;
            $hasDiscount = (bool) ($row['discount'] ?? false);
            $discountAmount = (float) ($row['discount_amount'] ?? 0);
            if (!$hasDiscount) {
                $discountAmount = 0;
            }
            $lineSubtotal = round(($unitPriceWithTax * $qty) - $discountAmount, 2);

            $details[] = [
                'variant' => $variant,
                'inventory' => $inventory,
                'quantity' => $qty,
                'unit_price' => round($unitPriceWithTax, 2),
                'sub_total' => $lineSubtotal,
                'discount' => $hasDiscount,
                'discount_amount' => $discountAmount,
                'unit_tax_amount' => $unitTaxAmount,
                'tax_percentage' => $taxPercentageApplied,
            ];
        }
        return $details;
    }

    private function calculateTotals(array $details): array
    {
        $total = 0.0;
        $totalTax = 0.0;
        $taxPercentageApplied = null;
        foreach ($details as $d) {
            $total += $d['sub_total'];
            $totalTax += round($d['unit_tax_amount'] * $d['quantity'], 2);
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

    private function generatePdf($entity, array $details, array $totals, string $quotationDate)
    {
        return Pdf::loadView('cashier.quotations.proforma', [
            'company' => Company::first(),
            'entity' => $entity,
            'details' => $details,
            'totals' => $totals,
            'quotation_date' => $quotationDate,
            'user' => Auth::user(),
        ])->setPaper('letter');
    }
}
