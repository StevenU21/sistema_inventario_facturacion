<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaleDetailsExport implements FromCollection, WithHeadings
{
    protected Sale $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale->load(['entity', 'paymentMethod', 'saleDetails.productVariant.product', 'saleDetails.productVariant.color', 'saleDetails.productVariant.size']);
    }

    public function collection()
    {
        $rows = [];
        $client = $this->sale->entity?->short_name
            ?: trim(($this->sale->entity->first_name ?? '') . ' ' . ($this->sale->entity->last_name ?? ''))
            ?: '-';

        foreach ($this->sale->saleDetails as $detail) {
            $variant = $detail->productVariant;
            $product = $variant?->product;
            $color = $variant?->color?->name ?? '-';
            $size = $variant?->size?->name ?? '-';

            $rows[] = [
                'sale_id' => $this->sale->id,
                'cliente' => $client,
                'metodo' => $this->sale->paymentMethod->name ?? '-',
                'producto' => $product->name ?? '-',
                'color' => $color,
                'talla' => $size,
                'cantidad' => (int) $detail->quantity,
                'precio_unitario' => (float) $detail->unit_price,
                'descuento' => $detail->discount ? 'Sí' : 'No',
                'monto_descuento' => (float) ($detail->discount_amount ?? 0),
                'subtotal_fila' => (float) $detail->sub_total,
                'impuesto_unitario' => (float) ($detail->unit_tax_amount ?? 0),
                'creado' => optional($this->sale->created_at)->format('d/m/Y H:i:s'),
            ];
        }

        return new Collection($rows);
    }

    public function headings(): array
    {
        return [
            'ID Venta',
            'Cliente',
            'Método de pago',
            'Producto',
            'Color',
            'Talla',
            'Cantidad',
            'Precio Unitario',
            'Descuento',
            'Monto Descuento',
            'Subtotal Fila',
            'Impuesto Unitario',
            'Creado',
        ];
    }
}
