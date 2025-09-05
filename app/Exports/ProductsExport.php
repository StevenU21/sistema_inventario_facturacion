<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    /** @var Builder */
    protected $query;

    /**
     * @param Builder $query Eloquent query with necessary relations eager loaded
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $products = $this->query->get();

        $statusLabels = [
            'available' => 'Disponible',
            'discontinued' => 'Descontinuado',
            'out_of_stock' => 'Sin stock',
            'reserved' => 'Reservado',
        ];

        return $products->map(function ($product) use ($statusLabels) {
            $brand = $product->brand->name ?? '-';
            $category = $product->category->name ?? '-';
            $unit = $product->unitMeasure->name ?? '-';
            $tax = $product->tax->name . ' ' . ($product->tax->percentage ?? '-') . '%';
            $provider = $product->entity
                ? trim(($product->entity->first_name ?? '') . ' ' . ($product->entity->last_name ?? ''))
                : '-';
            $status = $statusLabels[$product->status] ?? ($product->status ?? '-');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $category,
                'brand' => $brand,
                'unit_measure' => $unit,
                'provider' => $provider,
                'status' => $status,
                'barcode' => $product->barcode,
                'tax' => $tax,
                'created_at' => optional($product->created_at)->format('d/m/Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Categoría',
            'Marca',
            'Medida',
            'Proveedor',
            'Estado',
            'Código de barras',
            'Impuesto',
            'Creado',
        ];
    }
}
