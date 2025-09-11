<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection, WithHeadings
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
        $purchases = $this->query->get();

        return $purchases->map(function ($purchase) {
            $supplier = $purchase->entity?->short_name
                ?: trim(($purchase->entity->first_name ?? '') . ' ' . ($purchase->entity->last_name ?? ''))
                ?: '-';
            $user = $purchase->user?->short_name ?? ($purchase->user?->name ?? '-');
            $firstProduct = optional($purchase->details->first()?->productVariant?->product)->name ?? '-';
            $totalQty = $purchase->details->sum('quantity');
            return [
                'id' => $purchase->id,
                'user' => $user,
                'product' => $firstProduct,
                'supplier' => $supplier,
                'warehouse' => $purchase->warehouse->name ?? '-',
                'method' => $purchase->paymentMethod->name ?? '-',
                'quantity' => $totalQty,
                'subtotal' => (float) ($purchase->subtotal ?? 0),
                'total' => (float) ($purchase->total ?? 0),
                'created_at' => optional($purchase->created_at)->format('d/m/Y H:i:s'),
                'updated_at' => optional($purchase->updated_at)->format('d/m/Y H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Producto',
            'Proveedor',
            'Almacén',
            'Método de pago',
            'Cantidad',
            'Subtotal',
            'Total',
            'Creado',
            'Actualizado',
        ];
    }
}
