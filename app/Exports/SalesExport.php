<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    /** @var Builder */
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $sales = $this->query->get();

        return $sales->map(function ($sale) {
            $client = $sale->entity?->short_name
                ?: trim(($sale->entity->first_name ?? '') . ' ' . ($sale->entity->last_name ?? ''))
                ?: '-';
            $user = $sale->user?->short_name ?? ($sale->user?->name ?? '-');
            $firstProduct = optional($sale->saleDetails->first()?->productVariant?->product)->name ?? '-';
            $totalQty = $sale->saleDetails->sum('quantity');
            $firstUnitPrice = optional($sale->saleDetails->first())->unit_price;
            return [
                'id' => $sale->id,
                'usuario' => $user,
                'producto' => $firstProduct,
                'cliente' => $client,
                'metodo' => $sale->paymentMethod->name ?? '-',
                'credito' => $sale->is_credit ? 'Sí' : 'No',
                'cantidad' => (int) $totalQty,
                'precio_unitario' => (float) ($firstUnitPrice ?? 0),
                'impuesto' => (float) ($sale->tax_amount ?? 0),
                'total' => (float) ($sale->total ?? 0),
                'creado' => optional($sale->created_at)->format('d/m/Y H:i:s'),
                'actualizado' => optional($sale->updated_at)->format('d/m/Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Producto',
            'Cliente',
            'Método de pago',
            'Crédito',
            'Cantidad',
            'Precio Unitario',
            'Impuesto',
            'Total',
            'Creado',
            'Actualizado',
        ];
    }
}
