<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoryMovementsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Producto',
            'Color',
            'Talla',
            'Almacén',
            'Tipo',
            'Referencia',
            'Notas',
            'Cantidad',
            'Precio Unitario',
            'Total',
            'Fecha creación',
            'Fecha actualización',
        ];
    }

    public function map($movement): array
    {
        $variant = optional(optional($movement->inventory)->productVariant);
        $product = $variant && $variant->product ? $variant->product->name : '-';
        $color = $variant && $variant->color ? $variant->color->name : '';
        $size = $variant && $variant->size ? $variant->size->name : '';
        return [
            $movement->id,
            $movement->user->short_name ?? '-',
            $product,
            $color,
            $size,
            optional($movement->inventory->warehouse)->name ?? '-',
            $movement->movement_type,
            $movement->reference,
            $movement->notes,
            $movement->quantity,
            $movement->unit_price,
            $movement->total_price,
            $movement->created_at,
            $movement->updated_at,
        ];
    }
}
