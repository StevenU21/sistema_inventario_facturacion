<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\Inventory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseDetailsExport implements FromCollection, WithHeadings
{
    protected Purchase $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase->load(['entity', 'warehouse', 'paymentMethod', 'details.productVariant.product']);
    }

    public function collection()
    {
        $rows = [];
        $supplier = $this->purchase->entity?->short_name
            ?: trim(($this->purchase->entity->first_name ?? '') . ' ' . ($this->purchase->entity->last_name ?? ''))
            ?: '-';

        foreach ($this->purchase->details as $detail) {
            $variant = $detail->productVariant;
            $product = $variant?->product;
            $color = $variant?->color?->name ?? '-';
            $size = $variant?->size?->name ?? '-';
            $inventory = Inventory::where('product_variant_id', $variant->id ?? null)
                ->where('warehouse_id', $this->purchase->warehouse_id)
                ->first();

            $rows[] = [
                'purchase_id' => $this->purchase->id,
                'reference' => $this->purchase->reference,
                'supplier' => $supplier,
                'warehouse' => $this->purchase->warehouse->name ?? '-',
                'product' => $product->name ?? '-',
                'color' => $color,
                'size' => $size,
                'quantity' => (int) $detail->quantity,
                'unit_price' => (float) $detail->unit_price,
                'sale_price' => (float) ($inventory->sale_price ?? 0),
                'stock' => (int) ($inventory->stock ?? 0),
                'min_stock' => (int) ($inventory->min_stock ?? 0),
                'row_subtotal' => (float) $detail->quantity * (float) $detail->unit_price,
                'created_at' => optional($this->purchase->created_at)->format('d/m/Y H:i:s'),
            ];
        }

        return new Collection($rows);
    }

    public function headings(): array
    {
        return [
            'ID Compra',
            'Referencia',
            'Proveedor',
            'Almacén',
            'Producto',
            'Color',
            'Talla',
            'Cantidad',
            'Precio Unitario',
            'Precio Venta',
            'Stock',
            'Stock Mínimo',
            'Total',
            'Creado',
        ];
    }
}
