<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Entity;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Inventory;
use App\Models\ProductVariant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    /**
     * Genera una proforma (sin persistir en BD) y devuelve el PDF y datos calculados.
     *
     * Payload esperado:
     * - entity_id: int
    * - warehouse_id: int (deprecated: use items[*].warehouse_id)
     * - quotation_date?: Y-m-d
     * - items: [ { product_variant_id:int, quantity:int, discount?:bool, discount_amount?:float } ]
     *
     * @return array{pdf: \Barryvdh\DomPDF\PDF, details: array, totals: array}
     */
    public function createQuotation(array $payload): array
    {
        $items = $payload['items'] ?? [];
    $details = $this->calculateDetails($items);
        $totals = $this->calculateTotals($details);

        $entity = Entity::find($payload['entity_id']);
        $quotationDate = $payload['quotation_date'] ?? now()->toDateString();

        // compatibilidad con la vista: asegurar sub_total presente
        $totalsForView = array_merge([
            'sub_total' => max(0, ($totals['total'] ?? 0) - ($totals['totalTax'] ?? 0)),
        ], $totals);

        $pdf = $this->generatePdf($entity, $details, $totalsForView, $quotationDate);

        return [
            'pdf' => $pdf,
            'details' => $details,
            'totals' => $totalsForView,
        ];
    }

    /**
     * Persiste la cotización en BD con estado pendiente y genera PDF.
    * Espera entity_id y items con warehouse_id por línea.
     * Devuelve la entidad Quotation creada y el PDF.
     *
     * @return array{quotation: Quotation, pdf: \Barryvdh\DomPDF\PDF}
     */
    public function storeQuotation(array $payload): array
    {
        $user = Auth::user();
        $items = $payload['items'] ?? [];
        $entity = Entity::findOrFail($payload['entity_id']);

    $details = $this->calculateDetails($items);
        $totals = $this->calculateTotals($details);

        $validUntil = now()->addDays(7)->toDateString();

        $quotation = DB::transaction(function () use ($entity, $user, $details, $totals, $validUntil) {
            $q = Quotation::create([
                'total' => $totals['total'],
                'status' => 'pending',
                'valid_until' => $validUntil,
                'user_id' => $user->id,
                'entity_id' => $entity->id,
            ]);

            foreach ($details as $d) {
                QuotationDetail::create([
                    'quotation_id' => $q->id,
                    'product_variant_id' => $d['variant']->id,
                    'quantity' => $d['quantity'],
                    'unit_price' => $d['unit_price'],
                    'discount' => $d['discount'],
                    'discount_amount' => $d['discount_amount'],
                    'sub_total' => $d['sub_total'],
                ]);
            }

            return $q->load(['entity', 'QuotationDetails.productVariant.product', 'user']);
        });

        $pdf = $this->generatePdf($entity, $details, [
            // mantener compatibilidad con la vista
            'sub_total' => $totals['total'] - ($totals['totalTax'] ?? 0),
            'total' => $totals['total'],
            'totalTax' => $totals['totalTax'] ?? 0,
            'taxPercentageApplied' => $totals['taxPercentageApplied'] ?? null,
        ], now()->toDateString(), $quotation);

        return ['quotation' => $quotation, 'pdf' => $pdf];
    }

    private function calculateDetails(array $items): array
    {
        $details = [];
        foreach ($items as $row) {
            $variant = ProductVariant::with(['product.tax'])->findOrFail($row['product_variant_id']);
            $warehouseId = (int) ($row['warehouse_id'] ?? 0);
            $inventory = Inventory::where('product_variant_id', $variant->id)
                ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
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

    private function generatePdf($entity, array $details, array $totals, string $quotationDate, ?Quotation $quotation = null)
    {
        return Pdf::loadView('cashier.quotations.proforma', [
            'company' => Company::first(),
            'entity' => $entity,
            'details' => $details,
            'totals' => $totals,
            'quotation_date' => $quotationDate,
            'user' => Auth::user(),
            'quotation' => $quotation,
        ])->setPaper('letter');
    }
}
