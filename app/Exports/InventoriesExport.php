<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventoriesExport implements FromQuery, WithHeadings, WithMapping
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
            'Producto',
            'Almacén',
            'Stock',
            'Stock mínimo',
            'Precio compra',
            'Precio venta',
            'Fecha creación',
            'Fecha actualización',
        ];
    }

    public function map($inventory): array
    {
        return [
            $inventory->id,
            $inventory->product->name ?? '-',
            $inventory->warehouse->name ?? '-',
            $inventory->stock,
            $inventory->min_stock,
            $inventory->purchase_price,
            $inventory->sale_price,
            $inventory->created_at,
            $inventory->updated_at,
        ];
    }
}
